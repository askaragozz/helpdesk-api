<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    private function isAdmin(User $user): bool
    {
        return ($user->role ?? null) === 'admin';
    }

    private function isOwner(User $user, Ticket $ticket): bool
    {
        return (int) $ticket->user_id === (int) $user->id;
    }

    private function isAssignee(User $user, Ticket $ticket): bool
    {
        return (int) ($ticket->assigned_to ?? 0) === (int) $user->id;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user) || $this->isOwner($user, $ticket) || $this->isAssignee($user, $ticket);
    }

    // Agent/admin can assign tickets
    public function assign(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user) || $this->isAssignee($user, $ticket);
    }

    // Status changes: assignee/admin only (matches your lifecycle intent)
    public function setStatus(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user) || $this->isAssignee($user, $ticket);
    }

    // Comments: anyone who can view can add public comments
    public function comment(User $user, Ticket $ticket): bool
    {
        return true;
    }

    // Internal comments: assignee/admin only
    public function internalComment(User $user, Ticket $ticket): bool
    {
        return $user->isAgent();
    }

    // Admin-only list scope=all
    public function viewAll(User $user): bool
    {
        return $this->isAdmin($user);
    }

    // Who can see internal notes
    public function viewInternalNotes(User $user, Ticket $ticket): bool
    {
        return $this->isAdmin($user) || $this->isAssignee($user, $ticket);
    }
}
