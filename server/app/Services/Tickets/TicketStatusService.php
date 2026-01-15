<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class TicketStatusService
{
    private const FLOW = [
        'open' => ['in_progress'],
        'in_progress' => ['resolved'],
        'resolved' => ['closed'],
        'closed' => [],
    ];

    public function transition(Ticket $ticket, string $toStatus, User $actor, ?string $note = null): Ticket
    {
        $from = $ticket->status;

        if (!array_key_exists($from, self::FLOW)) {
            throw ValidationException::withMessages([
                'status' => "Unknown current status: {$from}",
            ]);
        }

        if (!in_array($toStatus, self::FLOW[$from], true)) {
            throw ValidationException::withMessages([
                'status' => "Invalid transition: {$from} → {$toStatus}",
            ]);
        }

        $ticket->status = $toStatus;
        $ticket->save();

        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'actor_id' => $actor->id,
            'from_status' => $from,
            'to_status' => $toStatus,
            'note' => $note,
        ]);

        return $ticket->fresh();
    }
}
