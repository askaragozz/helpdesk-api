<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Helpdesk')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row min-vh-100">

        {{-- Sidebar --}}
        <aside class="col-md-3 col-lg-2 d-none d-md-block bg-dark text-white p-0">
            @include('layouts.partials.sidebar')
        </aside>

        {{-- Main --}}
        <div class="col-md-9 col-lg-10 p-0 d-flex flex-column">
            @include('layouts.partials.navbar')

            <main class="flex-fill p-4">
                @include('layouts.partials.flash')
                @yield('content')
            </main>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
