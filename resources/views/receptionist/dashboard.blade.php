@extends('layouts.app')
@section('title','Reception Dashboard — CityCare')
@section('page-title','Reception Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-calendar-day-fill"></i></div>
        <div class="stat-value">{{ $todayAppointments->count() }}</div>
        <div class="stat-label">Today's appointments</div>
        <div class="stat-accent" style="background:var(--info)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-light);color:var(--warning)"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value">{{ $todayAppointments->where('status','pending')->count() }}</div>
        <div class="stat-label">Pending today</div>
        <div class="stat-accent" style="background:var(--warning)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-value">{{ $todayAppointments->where('status','confirmed')->count() }}</div>
        <div class="stat-label">Confirmed today</div>
        <div class="stat-accent" style="background:var(--success)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-light);color:var(--primary)"><i class="bi bi-calendar-week"></i></div>
        <div class="stat-value">{{ $upcomingAppointments->count() }}</div>
        <div class="stat-label">Upcoming</div>
        <div class="stat-accent" style="background:var(--primary)"></div>
    </div></div>
</div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card"><div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-day me-2"></i>Today's schedule</span>
            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Book</a>
        </div><div class="card-body p-0">
        <div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Slot</th><th>Patient</th><th>Doctor</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($todayAppointments as $appt)
            <tr>
                <td><strong>{{ $appt->appointment_slot }}</strong></td>
                <td>{{ $appt->patient->user->name??'—' }}</td>
                <td>Dr. {{ $appt->doctor->user->name??'—' }}</td>
                <td><x-status-badge :status="$appt->status" /></td>
                <td><a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-3 text-muted">No appointments today.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-header">Quick actions</div><div class="card-body d-grid gap-2">
            <a href="{{ route('appointments.create') }}" class="btn btn-primary"><i class="bi bi-calendar-plus me-2"></i>Book appointment</a>
            <a href="{{ route('patients.create') }}" class="btn btn-outline-primary"><i class="bi bi-person-plus me-2"></i>Register patient</a>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-people me-2"></i>Search patients</a>
            <a href="{{ route('reports.appointments') }}" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-bar-graph me-2"></i>Appointment report</a>
        </div></div>
    </div>
</div>
@endsection
