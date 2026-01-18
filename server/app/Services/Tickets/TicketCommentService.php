<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketCommentRead;
use App\Models\User;

class TicketCommentService
{
    public function list(Ticket $ticket, User $user, bool $canSeeInternal)
    {
        return $ticket->comments()
            ->with('author:id,name,email')
            ->when(!$canSeeInternal, fn ($q) => $q->where('visibility', 'public'))
            ->orderBy('id')
            ->get();
    }

    public function add(
        Ticket $ticket,
        User $user,
        string $body,
        string $visibility = 'public'
    ): TicketComment {
        return TicketComment::create([
            'ticket_id' => $ticket->id,
            'author_id' => $user->id,
            'body' => $body,
            'visibility' => $visibility,
        ]);
    }

    public function unreadCount(
        Ticket $ticket,
        User $user,
        bool $canSeeInternal
    ): int {
        $lastReadAt = TicketCommentRead::query()
            ->where('ticket_id', $ticket->id)
            ->where('user_id', $user->id)
            ->value('last_read_at');

        return TicketComment::query()
            ->where('ticket_id', $ticket->id)
            ->when(!$canSeeInternal, fn ($q) => $q->where('visibility', 'public'))
            ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
            ->count();
    }

    public function markRead(Ticket $ticket, User $user): void
    {
        TicketCommentRead::updateOrCreate(
            ['ticket_id' => $ticket->id, 'user_id' => $user->id],
            ['last_read_at' => now()]
        );
    }
}
