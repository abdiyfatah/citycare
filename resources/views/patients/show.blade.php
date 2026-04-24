@extends('layouts.app')
@section('title', $patient->user->name . ' — CityCare')
@section('page-title', 'Patient Profile')

@section('content')
<div class="page-header">
    <div>
        <h2>{{ $patient->user->name }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->hasRole(['admin','receptionist']))
        <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-plus me-1"></i> Book appointment
        </a>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endif
    </div>
</div>

<div class="row g-3">
    {{-- Demographics --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <div class="avatar-lg mx-auto mb-3" style="width:72px; height:72px; font-size:28px">
                    {{ strtoupper(substr($patient->user->name, 0, 1)) }}
                </div>
                <h5 class="mb-0">{{ $patient->user->name }}</h5>
                <div class="text-muted" style="font-size:13px">{{ $patient->user->email }}</div>
                <span class="badge mt-2" style="background:var(--primary-light); color:var(--primary)">
                    {{ $patient->patient_number }}
                </span>
            </div>
            <div class="card-body pt-0">
                @foreach([
                    ['Gender',     ucfirst($patient->gender ?? '—')],
                    ['Age',        ($patient->age ? $patient->age.' years' : '—')],
                    ['DOB',        $patient->date_of_birth?->format('d M Y') ?? '—'],
                    ['Phone',      $patient->phone ?? '—'],
                    ['Blood group',$patient->blood_group ?? '—'],
                ] as [$label, $value])
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border); font-size:13.5px">
                    <span class="text-muted">{{ $label }}</span>
                    <strong>{{ $value }}</strong>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Medical info --}}
        <div class="card">
            <div class="card-header">Medical notes</div>
            <div class="card-body" style="font-size:13.5px">
                @if($patient->allergies)
                <div class="mb-2">
                    <span class="badge badge-cancelled me-1">Allergies</span>
                    {{ $patient->allergies }}
                </div>
                @endif
                {{ $patient->medical_notes ?? 'No notes recorded.' }}
            </div>
        </div>
    </div>

    {{-- Appointment history --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Appointment history</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr>
                            <th>Date</th><th>Doctor</th><th>Status</th><th>Payment</th><th></th>
                        </tr></thead>
                        <tbody>
                        @forelse($appointments as $appt)
                        <tr>
                            <td>{{ $appt->appointment_date->format('d M Y') }}
                                <div style="font-size:11px; color:var(--text-muted)">{{ $appt->appointment_slot }}</div>
                            </td>
                            <td>Dr. {{ $appt->doctor->user->name ?? '—' }}</td>
                            <td><x-status-badge :status="$appt->status" /></td>
                            <td>
                                @if($appt->payment && $appt->payment->status === 'paid')
                                    <span style="font-size:12px; color:var(--success); font-weight:600">
                                        UGX {{ number_format($appt->payment->amount) }}
                                    </span>
                                @else
                                    <span style="font-size:12px; color:var(--text-muted)">Unpaid</span>
                                @endif
                            </td>
                            <td><a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">No appointments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($appointments->hasPages())
            <div class="card-body py-2 border-top">{{ $appointments->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
