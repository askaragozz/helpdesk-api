<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm" style="width: 360px;">
        <div class="card-body">

            <h5 class="mb-3">Login</h5>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>

                @error('email')
                    <div class="text-danger small mb-2">
                        {{ $message }}
                    </div>
                @enderror

                <button class="btn btn-primary w-100">
                    Login
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
