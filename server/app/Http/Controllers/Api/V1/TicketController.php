<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketComment;
use App\Models\TicketCommentRead;

use App\Services\Tickets\TicketStatusService;
use App\Services\Tickets\TicketService;
use App\Services\Tickets\TicketCommentService;

use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $query = Ticket::query()
            ->with(['creator', 'assignee'])
            ->withCount('comments');

        // apply your role-based scopes here (requester/agent/admin)
        // e.g. requester only own:
        // if ($request->user()->role === 'requester') $query->where('created_by', $request->user()->id);

        $tickets = $query->latest()->paginate(15);

        return TicketResource::collection($tickets);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $canSeeInternal = $request->user()->can('viewInternalNotes', $ticket);
        $lastReadAt = $ticket->last_read_at; // adjust if you store per-user elsewhere

        $unreadCount = TicketComment::query()
            ->where('ticket_id', $ticket->id)
            ->when(!$canSeeInternal, fn ($qq) => $qq->where('visibility', 'public'))
            ->when($lastReadAt, fn ($qq) => $qq->where('created_at', '>', $lastReadAt))
            ->count();

        $ticket->load([
            'creator',
            'assignee',
            'comments' => fn ($q) => $q
                ->when(!$canSeeInternal, fn ($qq) => $qq->where('visibility', 'public'))
                ->latest()
                ->with('author'),
        ]);

        $ticket->unread_comments_count = $unreadCount;

        return TicketResource::make($ticket);
    }

    public function store(Request $request, TicketService $service)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        $ticket = $service->create($data, $request->user());

        return response()->json([
            'data' => new TicketResource($ticket),
        ], 201);
    }

    public function update(Request $request, Ticket $ticket, TicketService $service)
    {
        if ($ticket->created_by !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:200'],
            'description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'in:low,medium,high'],
        ]);

        if (empty($data)) {
            return response()->json(['message' => 'No valid fields'], 422);
        }

        $ticket = $service->update($ticket, $data);

        return response()->json(['data' => $ticket]);
    }

    public function assign(Request $request, Ticket $ticket, TicketService $service)
    {
        if (!$request->user()->isAgent()) {
            abort(403);
        }

        $data = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $ticket = $service->assign($ticket, $data['assigned_to'] ?? null);

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

    public function listComments(
        Request $request,
        Ticket $ticket,
        TicketCommentService $service
    ) {
        $this->authorize('view', $ticket);

        $canSeeInternal = $request->user()->can('viewInternalNotes', $ticket);

        $comments = $service->list($ticket, $request->user(), $canSeeInternal);
        $unread = $service->unreadCount($ticket, $request->user(), $canSeeInternal);

        return response()->json([
            'data' => $comments,
            'meta' => [
                'unread_count' => $unread,
            ],
        ]);
    }


    public function addComment(
        Request $request,
        Ticket $ticket,
        TicketCommentService $service
    ) {
        $this->authorize('comment', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'visibility' => ['nullable', 'in:public,internal'],
        ]);

        $visibility = $data['visibility'] ?? 'public';

        if ($visibility === 'internal') {
            $this->authorize('internalComment', $ticket);
        }

        $comment = $service->add(
            $ticket,
            $request->user(),
            $data['body'],
            $visibility
        );

        return response()->json([
            'data' => $comment->load('author:id,name,email'),
        ], 201);
    }



    public function markCommentsRead(
        Request $request,
        Ticket $ticket,
        TicketCommentService $service
    ) {
        $this->authorize('view', $ticket);

        $service->markRead($ticket, $request->user());

        return response()->json(['message' => 'OK']);
    }


}
