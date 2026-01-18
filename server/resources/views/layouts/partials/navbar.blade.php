<nav class="navbar navbar-expand bg-white border-bottom px-4">
    <span class="navbar-brand mb-0 h1">
        @yield('header', 'Dashboard')
    </span>

    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="text-muted small">
            {{ auth()->user()->name ?? 'Guest' }}
        </span>

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">
                    Logout
                </button>
            </form>
        @endauth
    </div>
</nav>
