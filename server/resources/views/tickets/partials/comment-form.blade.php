<form
    method="POST"
    action="{{ route('tickets.comments.store', $ticket) }}"
    class="card mt-4"
>
    @csrf

    <div class="card-body">
        <h6 class="card-title">Add Comment</h6>

        <div class="mb-3">
            <textarea
                name="body"
                rows="4"
                class="form-control @error('body') is-invalid @enderror"
                placeholder="Write your comment..."
                required
            >{{ old('body') }}</textarea>

            @error('body')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @can('InternalComment', $ticket)
            <div class="mb-3">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="visibility"
                        value="internal"
                        id="internalComment"
                        {{ old('visibility') === 'internal' ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="internalComment">
                        Internal note (visible to staff only)
                    </label>
                </div>
            </div>
        @endcan

        <button type="submit" class="btn btn-primary">
            Post Comment
        </button>
    </div>
</form>
