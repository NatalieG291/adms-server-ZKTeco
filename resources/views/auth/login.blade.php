<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display:flex; min-height:100vh; align-items:center; justify-content:center; background:#f5f7fb; }
        .login-card { width: 100%; max-width: 380px; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="card login-card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Sign in</h5>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>


