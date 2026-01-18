<div class="card mb-4">
    <div class="card-header bg-white">
        <strong>Conversation</strong>
    </div>

    <div class="card-body">

        @forelse ($ticket->comments as $comment)

            @if ($comment->visibility === 'internal' && !auth()->user()->can('viewInternalNotes', $ticket))
                @continue
            @endif

            <div class="mb-4">
                <div class="small text-muted mb-1 d-flex justify-content-between">
                    <span>
                        {{ $comment->author->name }}
                        ·
                        {{ $comment->visibility === 'internal' ? 'Internal Note' : 'Comment' }}
                    </span>
                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                </div>

                <div class="border rounded p-3
                    {{ $comment->visibility === 'internal'
                        ? 'bg-warning-subtle border-warning'
                        : 'bg-light' }}">
                    {{ $comment->body }}
                </div>
            </div>

        @empty
            <p class="text-muted mb-0">No comments yet.</p>
        @endforelse

    </div>
</div>
