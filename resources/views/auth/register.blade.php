<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CityCare Medical Centre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:500px">
        <div class="auth-logo">
            <i class="bi bi-heart-pulse-fill"></i>
            <h1>Create Account</h1>
            <p>Register as a new patient at CityCare</p>
        </div>

        @if($errors->any())
            <x-alert type="danger" :message="$errors->first()" />
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone (optional)</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">— Select —</option>
                        <option value="male"   @selected(old('gender') === 'male')>Male</option>
                        <option value="female" @selected(old('gender') === 'female')>Female</option>
                        <option value="other"  @selected(old('gender') === 'other')>Other</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-4">Create account</button>
        </form>
        <hr class="my-3">
        <p class="text-center" style="font-size:13px; color:var(--text-muted)">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--primary); font-weight:500">Sign in</a>
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
