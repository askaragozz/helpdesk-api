<div class="d-flex flex-column h-100">
    <div class="p-3 border-bottom border-secondary fw-bold">
        Helpdesk
    </div>

    <ul class="nav nav-pills flex-column p-3 gap-1">

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-white">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('tickets.index') }}" class="nav-link text-white">
                Tickets
            </a>
        </li>

    </ul>
</div>
