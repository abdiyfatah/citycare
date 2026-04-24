@extends('layouts.app')
@section('title', 'Admin Dashboard — CityCare')
@section('page-title', 'Admin Dashboard')

@section('content')
{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4f5; color:var(--primary)">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_patients']) }}</div>
            <div class="stat-label">Total patients</div>
            <div class="stat-accent" style="background:var(--primary)"></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4e3; color:var(--warning)">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['total_doctors'] }}</div>
            <div class="stat-label">Active doctors</div>
            <div class="stat-accent" style="background:var(--warning)"></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fb; color:var(--info)">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="stat-value">{{ $stats['today_appointments'] }}</div>
            <div class="stat-label">Today's appointments</div>
            <div class="stat-accent" style="background:var(--info)"></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f5ed; color:var(--success)">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value">UGX {{ number_format($stats['revenue_this_month']) }}</div>
            <div class="stat-label">Revenue this month</div>
            <div class="stat-accent" style="background:var(--success)"></div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Today's appointments --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-calendar-day me-2"></i>Today's appointments</span>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="card-body p-0">
                @if($todayAppointments->isEmpty())
                    <p class="text-muted text-center py-4">No appointments scheduled for today.</p>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th>Slot</th><th>Patient</th><th>Doctor</th><th>Status</th><th></th>
                        </tr></thead>
                        <tbody>
                        @foreach($todayAppointments as $appt)
                        <tr>
                            <td><strong>{{ $appt->appointment_slot }}</strong></td>
                            <td>{{ $appt->patient->user->name ?? '—' }}</td>
                            <td>Dr. {{ $appt->doctor->user->name ?? '—' }}</td>
                            <td><x-status-badge :status="$appt->status" /></td>
                            <td>
                                <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent payments --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-credit-card me-2"></i>Recent payments</span>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary">All</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentPayments as $pay)
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-size:13px; font-weight:500">{{ $pay->appointment->patient->user->name ?? '—' }}</div>
                        <div style="font-size:11px; color:var(--text-muted)">{{ $pay->receipt_number }}</div>
                    </div>
                    <div style="font-weight:600; color:var(--success)">UGX {{ number_format($pay->amount) }}</div>
                </div>
                @empty
                    <p class="text-muted text-center py-3" style="font-size:13px">No payments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Quick links --}}
<div class="row g-3 mt-1">
    @foreach([
        ['route' => 'patients.create',     'icon' => 'person-plus',    'label' => 'Register patient',   'color' => 'primary'],
        ['route' => 'doctors.create',      'icon' => 'person-badge',   'label' => 'Add doctor',         'color' => 'warning'],
        ['route' => 'appointments.create', 'icon' => 'calendar-plus',  'label' => 'Book appointment',   'color' => 'info'],
        ['route' => 'reports.index',       'icon' => 'bar-chart-line', 'label' => 'Generate report',    'color' => 'success'],
    ] as $link)
    <div class="col-6 col-md-3">
        <a href="{{ route($link['route']) }}" class="card text-decoration-none text-center py-3 px-2 d-block"
           style="transition:.15s; border:2px solid transparent"
           onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='transparent'">
            <i class="bi bi-{{ $link['icon'] }}" style="font-size:24px; color:var(--{{ $link['color'] }})"></i>
            <div style="font-size:12.5px; font-weight:500; margin-top:6px; color:var(--text)">{{ $link['label'] }}</div>
        </a>
    </div>
    @endforeach
</div>
@endsection
