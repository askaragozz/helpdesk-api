<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\User;

class TicketService
{
    public function create(array $data, User $user): Ticket
    {
        return Ticket::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
            'created_by' => $user->id,
            'assigned_to' => null,
        ]);
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);
        return $ticket;
    }

    public function assign(Ticket $ticket, ?int $userId): Ticket
    {
        $ticket->assigned_to = $userId;
        $ticket->save();

        return $ticket;
    }

    public function setStatus(Ticket $ticket, string $status): Ticket
    {
        $ticket->status = $status;
        $ticket->save();

        return $ticket;
    }
}
