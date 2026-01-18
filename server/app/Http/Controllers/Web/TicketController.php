<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Tickets\TicketService;
use App\Services\Tickets\TicketCommentService;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('created_by', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    public function create()
    {
        return view('tickets.create');
    }


    public function store(Request $request, TicketService $service)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        $ticket = $service->create($data, $request->user());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket created.');
    }

    public function update(Request $request, Ticket $ticket, TicketService $service)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $service->update($ticket, $data);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated.');
    }

    
    public function storeComment(
        Request $request,
        Ticket $ticket,
        TicketCommentService $service
    ) {
        $this->authorize('comment', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $service->add($ticket, $request->user(), $data['body']);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Comment added.');
    }

}
