@extends('layouts.app')
@section('title', 'Reports — CityCare')
@section('page-title', 'Reports')

@section('content')
<div class="page-header">
    <h2>Reports & Exports</h2>
</div>

<div class="row g-3">
    @foreach([
        [
            'title' => 'Appointment report',
            'icon'  => 'calendar-check',
            'desc'  => 'Generate a list of appointments filtered by date range, status, or doctor.',
            'route' => 'reports.appointments',
            'color' => 'info',
        ],
        [
            'title' => 'Payment report',
            'icon'  => 'cash-coin',
            'desc'  => 'Revenue summary filtered by date. Export to CSV.',
            'route' => 'reports.payments',
            'color' => 'success',
        ],
        [
            'title' => 'Doctor schedule',
            'icon'  => 'person-badge',
            'desc'  => "View a doctor's full appointment list for a specific day.",
            'route' => 'reports.doctor-schedule',
            'color' => 'warning',
        ],
        [
            'title' => 'Patient visit history',
            'icon'  => 'clock-history',
            'desc'  => "View all of a patient's visits and export to CSV.",
            'route' => 'reports.patient-visits',
            'color' => 'primary',
        ],
    ] as $report)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-start gap-3 p-4">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--{{ $report['color'] === 'primary' ? 'primary-light' : ($report['color'] === 'info' ? 'info-light' : ($report['color'] === 'success' ? 'success-light' : 'warning-light')) }}); display:flex; align-items:center; justify-content:center; flex-shrink:0">
                    <i class="bi bi-{{ $report['icon'] }}" style="font-size:20px; color:var(--{{ $report['color'] }})"></i>
                </div>
                <div>
                    <h6 style="font-weight:600; margin-bottom:4px">{{ $report['title'] }}</h6>
                    <p class="text-muted mb-3" style="font-size:13px">{{ $report['desc'] }}</p>
                    <a href="{{ route($report['route']) }}" class="btn btn-sm btn-outline-primary">
                        Generate <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
