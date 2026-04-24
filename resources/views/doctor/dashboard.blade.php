@extends('layouts.app')
@section('title','Doctor Dashboard — CityCare')
@section('page-title','My Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-calendar-day-fill"></i></div>
        <div class="stat-value">{{ $stats['today_count'] }}</div><div class="stat-label">Today's patients</div>
        <div class="stat-accent" style="background:var(--info)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-light);color:var(--warning)"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value">{{ $stats['pending_today'] }}</div><div class="stat-label">Pending today</div>
        <div class="stat-accent" style="background:var(--warning)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-check2-circle"></i></div>
        <div class="stat-value">{{ $stats['completed_today'] }}</div><div class="stat-label">Completed today</div>
        <div class="stat-accent" style="background:var(--success)"></div>
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-light);color:var(--primary)"><i class="bi bi-people"></i></div>
        <div class="stat-value">{{ $stats['total_patients'] }}</div><div class="stat-label">Total patients seen</div>
        <div class="stat-accent" style="background:var(--primary)"></div>
    </div></div>
</div>
<div class="row g-3">
    <div class="col-lg-8"><div class="card">
        <div class="card-header"><i class="bi bi-calendar-day me-2"></i>Today's appointments</div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Slot</th><th>Patient</th><th>Reason</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($todayAppointments as $appt)
            <tr>
                <td><strong>{{ $appt->appointment_slot }}</strong></td>
                <td>{{ $appt->patient->user->name??'—' }}<div style="font-size:11px;color:var(--text-muted)">{{ $appt->patient->patient_number }}</div></td>
                <td style="font-size:13px;color:var(--text-muted)">{{ Str::limit($appt->reason,40) }}</td>
                <td><x-status-badge :status="$appt->status" /></td>
                <td><a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">Consult</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-3 text-muted">No appointments today.</td></tr>
            @endforelse
            </tbody>
        </table></div></div>
    </div></div>
    <div class="col-lg-4"><div class="card">
        <div class="card-header">Upcoming</div>
        <div class="card-body p-0">
        @forelse($upcomingAppointments as $appt)
        <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--border)">
            <div>
                <div style="font-size:13px;font-weight:500">{{ $appt->patient->user->name??'—' }}</div>
                <div style="font-size:11px;color:var(--text-muted)">{{ $appt->appointment_date->format('d M') }} at {{ $appt->appointment_slot }}</div>
            </div>
            <x-status-badge :status="$appt->status" />
        </div>
        @empty
        <p class="text-muted text-center py-3" style="font-size:13px">No upcoming appointments.</p>
        @endforelse
        </div>
    </div></div>
</div>
@endsection
