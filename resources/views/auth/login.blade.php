<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CityCare Medical Centre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1>CityCare</h1>
            <p>Medical Centre Management System</p>
        </div>

        @if($errors->any())
            <x-alert type="danger" :message="$errors->first()" />
        @endif
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••" required>
            </div>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>

        <hr class="my-3">
        <p class="text-center" style="font-size:13px; color:var(--text-muted)">
            New patient?
            <a href="{{ route('register') }}" style="color:var(--primary); font-weight:500">Create an account</a>
        </p>

        {{-- Demo credentials --}}
        <div class="mt-3 p-3" style="background:var(--bg); border-radius:8px; font-size:12px; color:var(--text-muted)">
            <strong>Demo credentials:</strong><br>
            admin@citycare.com / password<br>
            reception@citycare.com / password<br>
            doctor@citycare.com / password<br>
            cashier@citycare.com / password
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
