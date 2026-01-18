<tr>
    <td>{{ $index }}</td>
    <td>
        <a href="{{ route('tickets.show', $ticket->id) }}">
            {{ $ticket->title }}
        </a>
    </td>
    <td>
        <span class="badge bg-warning text-dark">
            {{ $ticket->status }}
        </span>
    </td>
    <td>
        <span class="badge bg-danger">
            {{ $ticket->priority }}
        </span>
    </td>
    <td>{{ $ticket->updated_at }}</td>
</tr>