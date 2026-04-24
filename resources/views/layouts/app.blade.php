<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CityCare Medical Centre')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-brand">
        <i class="bi bi-heart-pulse-fill"></i>
        <span>CityCare</span>
    </div>

    <ul class="sidebar-nav flex-grow-1">
        {{-- Admin links --}}
        @if(auth()->user()->isAdmin())
        <li class="nav-section">Admin</li>
        <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Departments</a></li>
        <li><a href="{{ route('doctors.index') }}" class="{{ request()->routeIs('doctors.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Doctors</a></li>
        <li><a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Patients</a></li>
        <li><a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Payments</a></li>
        <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Reports</a></li>
        @endif

        {{-- Receptionist links --}}
        @if(auth()->user()->isReceptionist())
        <li class="nav-section">Reception</li>
        <li><a href="{{ route('receptionist.dashboard') }}" class="{{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Patients</a></li>
        <li><a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Reports</a></li>
        @endif

        {{-- Doctor links --}}
        @if(auth()->user()->isDoctor())
        <li class="nav-section">Doctor</li>
        <li><a href="{{ route('doctor.dashboard') }}" class="{{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> My Appointments</a></li>
        @endif

        {{-- Cashier links --}}
        @if(auth()->user()->isCashier())
        <li class="nav-section">Cashier</li>
        <li><a href="{{ route('cashier.dashboard') }}" class="{{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Payments</a></li>
        <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Reports</a></li>
        @endif

        {{-- Patient links --}}
        @if(auth()->user()->isPatient())
        <li class="nav-section">Patient</li>
        <li><a href="{{ route('patient.dashboard') }}" class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> My Appointments</a></li>
        <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> My Payments</a></li>
        @endif
    </ul>

    {{-- User profile at bottom --}}
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</nav>

{{-- Main content area --}}
<div id="main-content">
    {{-- Topbar --}}
    <header class="topbar">
        <button id="sidebar-toggle" class="btn-icon">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title">@yield('page-title', 'CityCare')</h1>
        <div class="topbar-right">
            <span class="badge-role">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </header>

    {{-- Alerts --}}
    <div class="alerts-wrapper">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-alert type="danger" :message="session('error')" />
        @endif
        @if($errors->any())
            <x-alert type="danger" :message="$errors->first()" />
        @endif
    </div>

    {{-- Page content --}}
    <main class="page-content">
        @yield('content')
    </main>

    <footer class="page-footer">
        <p>&copy; {{ date('Y') }} CityCare Medical Centre. All rights reserved.</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle
    document.getElementById('sidebar-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('main-content').classList.toggle('expanded');
    });
</script>
@stack('scripts')
</body>
</html>
