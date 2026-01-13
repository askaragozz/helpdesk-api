<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->where('created_by', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json(['data' => $tickets]);
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

    
}
