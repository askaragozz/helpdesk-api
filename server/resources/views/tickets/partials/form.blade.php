<div class="card">
    <div class="card-body">
        

        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div class="mb-3">
                <label>Subject</label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    class="form-control"
                >
            </div>

            <div class="mb-3">
                <label>Message</label>
                <textarea
                    name="description"
                    class="form-control"
                    rows="5"
                >{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Create Ticket
            </button>
        </form>

    </div>
</div>
