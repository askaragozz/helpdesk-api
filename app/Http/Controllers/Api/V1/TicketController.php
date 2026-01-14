<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketComment;
use App\Models\TicketCommentRead;

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

    public function listComments(Request $request, \App\Models\Ticket $ticket)
    {
        $user = $request->user();

        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $q = $ticket->comments()
            ->with('author:id,name,email')
            ->orderBy('id');

        // owner can’t see internal notes
        if (!($this->isAdmin($user) || $this->isAssignee($ticket, $user))) {
            $q->where('visibility', 'public');
        }

        $comments = $q->get();

        $lastReadAt = TicketCommentRead::query()
            ->where('ticket_id', $ticket->id)
            ->where('user_id', $user->id)
            ->value('last_read_at');

        $unreadCount = TicketComment::query()
            ->where('ticket_id', $ticket->id)
            ->when(!($this->isAdmin($user) || $this->isAssignee($ticket, $user)), fn ($qq) => $qq->where('visibility', 'public'))
            ->when($lastReadAt, fn ($qq) => $qq->where('created_at', '>', $lastReadAt))
            ->count();

        return response()->json([
            'data' => $comments,
            'meta' => [
                'last_read_at' => $lastReadAt,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    public function addComment(Request $request, \App\Models\Ticket $ticket)
    {
        $user = $request->user();

        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1'],
            'visibility' => ['nullable', 'string', 'in:public,internal'],
        ]);

        $visibility = $data['visibility'] ?? 'public';

        // internal notes: only assignee/admin
        if ($visibility === 'internal' && !($this->isAdmin($user) || $this->isAssignee($ticket, $user))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'author_id' => $user->id,
            'body' => $data['body'],
            'visibility' => $visibility,
        ]);

        return response()->json([
            'data' => $comment->load('author:id,name,email'),
        ], 201);
    }

    public function markCommentsRead(Request $request, \App\Models\Ticket $ticket)
    {
        $user = $request->user();

        if (!$this->canAccessTicket($ticket, $user)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $now = now();

        TicketCommentRead::query()->updateOrCreate(
            ['ticket_id' => $ticket->id, 'user_id' => $user->id],
            ['last_read_at' => $now]
        );

        return response()->json([
            'message' => 'OK',
            'data' => [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'last_read_at' => $now->toISOString(),
            ],
        ]);
    }


    private function isAdmin($user): bool
    {
        return ($user->role ?? null) === 'admin';
    }

    private function isAssignee($ticket, $user): bool
    {
        return (int) ($ticket->assigned_to ?? 0) === (int) $user->id;
    }

    private function isOwner($ticket, $user): bool
    {
        return (int) ($ticket->user_id ?? 0) === (int) $user->id;
    }

    private function canAccessTicket($ticket, $user): bool
    {
        return $this->isAdmin($user) || $this->isAssignee($ticket, $user) || $this->isOwner($ticket, $user);
    }

}
