<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Tickets\TicketStatusService;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function index(Request $request)
    {

        $data = $request->validate([
            'scope' => ['nullable', 'string', 'in:assigned,unassigned,all'],
            'status' => ['nullable', 'string', 'in:open,in_progress,resolved,closed'], // optional but useful
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        
        $user = $request->user();
        $scope = $data['scope'] ?? 'assigned';
        $perPage = $data['per_page'] ?? 20;

        $q = \App\Models\Ticket::query();

        if (!empty($data['status'])) {
            $q->where('status', $data['status']);
        }

        if ($scope === 'assigned') {
            $q->where('assigned_to', $user->id);
        } elseif ($scope === 'unassigned') {
            $q->whereNull('assigned_to');
        } elseif ($scope === 'all') {
            if (($user->role ?? null) !== 'admin') {
                return response()->json([ 'message' => 'Only admin can view all tickets.'], 403);
            }
        }


        $tickets = $q
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

            return response()->json($tickets);
    }

    public function show(Request $request, Ticket $ticket)
    {
        if ($ticket->created_by !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        return response()->json(['data' => $ticket]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        $ticket = Ticket::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
            'created_by' => $request->user()->id,
            'assigned_to' => null,
        ]);

        return response()->json([
            'data' => $ticket,
        ], 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        if ($ticket->created_by !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:200'],
            'description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'in:low,medium,high'],
        ]);

        // prevent empty payload
        if (empty($data)) {
            return response()->json([
                'message' => 'No valid fields provided.',
            ], 422);
        }

        $ticket->update($data);

        return response()->json(['data' => $ticket]);
    }

    public function assign(Request $request, Ticket $ticket)
    {
        if (!$request->user()->isAgent()) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $ticket->assigned_to = $data['assigned_to'] ?? null;
        $ticket->save();

        return response()->json(['data' => $ticket]);
    }

    public function setStatus(Request $request, Ticket $ticket)
    {
        if (!$request->user()->isAgent()) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $ticket->status = $data['status'];
        $ticket->save();

        return response()->json(['data' => $ticket]);
    }

    public function updateStatus(Request $request, \App\Models\Ticket $ticket, TicketStatusService $service)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,resolved,closed'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $updated = $service->transition(
            ticket: $ticket,
            toStatus: $data['status'],
            actor: $request->user(),
            note: $data['note'] ?? null
        );

        return response()->json([
            'data' => $updated,
        ]);
    }

    public function statusHistory(\App\Models\Ticket $ticket)
    {
        $items = $ticket->statusHistories()
            ->with('actor:id,name,email')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $items,
        ]);
    }

}
