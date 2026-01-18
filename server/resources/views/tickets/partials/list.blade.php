<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        {{-- Loop tickets here --}}
        @foreach ($tickets as $ticket)
            @include('tickets.partials.ticket', ['ticket' => $ticket, 'index' => $loop->iteration])
        @endforeach
    </tbody>
</table>