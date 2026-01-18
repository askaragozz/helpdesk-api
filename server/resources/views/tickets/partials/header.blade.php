<div class="card mb-4">
    <div class="card-body">

        <h5 class="card-title mb-3">
            Ticket #{{ $ticket->id }} - {{ $ticket->title }}
        </h5>

        <div class="mb-3">
            <span class="badge bg-warning text-dark me-1">{{ $ticket->status }}</span>
            <span class="badge bg-danger me-1">{{ $ticket->priority }}</span>
            <span class="badge bg-secondary">Billing</span>
        </div>

        <p class="text-muted mb-0">
            {{ $ticket->description }}
        </p>

    </div>
</div>
