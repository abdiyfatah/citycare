@extends('layouts.app')
@section('title','My Dashboard — CityCare')
@section('page-title','My Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-light);color:var(--primary)"><i class="bi bi-person-fill"></i></div>
        <div class="stat-value">{{ $patient->patient_number }}</div>
        <div class="stat-label">Patient number</div>
        <div class="stat-accent" style="background:var(--primary)"></div>
    </div></div>
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-value">{{ $upcomingAppointments->count() }}</div>
        <div class="stat-label">Upcoming appointments</div>
        <div class="stat-accent" style="background:var(--info)"></div>
    </div></div>
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-clock-history"></i></div>
        <div class="stat-value">{{ $recentVisits->count() }}</div>
        <div class="stat-label">Recent visits</div>
        <div class="stat-accent" style="background:var(--success)"></div>
    </div></div>
</div>
<div class="row g-3">
    <div class="col-lg-7"><div class="card">
        <div class="card-header">Upcoming appointments</div><div class="card-body p-0">
        <div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Date & time</th><th>Doctor</th><th>Department</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($upcomingAppointments as $appt)
            <tr>
                <td><strong>{{ $appt->appointment_date->format('d M Y') }}</strong>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $appt->appointment_slot }}</div></td>
                <td>Dr. {{ $appt->doctor->user->name??'—' }}</td>
                <td style="font-size:12px;color:var(--text-muted)">{{ $appt->doctor->department->name??'—' }}</td>
                <td><x-status-badge :status="$appt->status" /></td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-3 text-muted">No upcoming appointments.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        </div>
    </div></div>
    <div class="col-lg-5"><div class="card">
        <div class="card-header">Recent payments</div><div class="card-body p-0">
        @forelse($payments as $pay)
        <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--border)">
            <div>
                <div style="font-size:13px;font-weight:500">{{ $pay->receipt_number }}</div>
                <div style="font-size:11px;color:var(--text-muted)">{{ $pay->paid_at?->format('d M Y') }}</div>
            </div>
            <div class="text-end">
                <div style="font-weight:600;color:var(--success)">UGX {{ number_format($pay->amount) }}</div>
                <x-status-badge :status="$pay->status" />
            </div>
        </div>
        @empty
        <p class="text-muted text-center py-3" style="font-size:13px">No payment history.</p>
        @endforelse
        </div>
    </div></div>
</div>
@endsection
