<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Tickets\TicketCommentService;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function store( Request $request, Ticket $ticket, TicketCommentService $service) 
    {
        $this->authorize('comment', $ticket);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $service->add(
            $ticket,
            $request->user(),
            $data['body'],
            $request->input('visibility', 'public')
        );


        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Comment added.');
    }
}
