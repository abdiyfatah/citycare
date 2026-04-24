@extends('layouts.app')
@section('title', 'Appointments — CityCare')
@section('page-title', 'Appointments')

@section('content')
<div class="page-header">
    <div>
        <h2>Appointments</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Appointments</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->hasRole(['admin','receptionist']))
    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Book appointment
    </a>
    @endif
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4 col-lg-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search patient or doctor…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-3 col-lg-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach(['pending','confirmed','completed','cancelled','no_show'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="doctor_id" class="form-select form-select-sm">
                    <option value="">All doctors</option>
                    @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" @selected(request('doctor_id') == $doc->id)>Dr. {{ $doc->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th>#</th><th>Date & slot</th><th>Patient</th><th>Doctor</th>
                    <th>Status</th><th>Payment</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($appointments as $appt)
                <tr>
                    <td style="color:var(--text-muted); font-size:12px">{{ $appt->id }}</td>
                    <td>
                        <strong>{{ $appt->appointment_date->format('d M Y') }}</strong>
                        <div style="font-size:12px; color:var(--text-muted)">{{ $appt->appointment_slot }}</div>
                    </td>
                    <td>{{ $appt->patient->user->name ?? '—' }}
                        <div style="font-size:11px; color:var(--text-muted)">{{ $appt->patient->patient_number ?? '' }}</div>
                    </td>
                    <td>Dr. {{ $appt->doctor->user->name ?? '—' }}</td>
                    <td><x-status-badge :status="$appt->status" /></td>
                    <td>
                        @if($appt->payment && $appt->payment->status === 'paid')
                            <span class="badge" style="background:var(--success-light); color:var(--success)">Paid</span>
                        @else
                            <span class="badge badge-cancelled">Unpaid</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if($appt->isEditable() && auth()->user()->hasRole(['admin','receptionist']))
                        <a href="{{ route('appointments.edit', $appt) }}" class="btn btn-sm btn-outline-secondary ms-1">Edit</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No appointments found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
    <div class="card-body py-2 border-top">
        {{ $appointments->links() }}
    </div>
    @endif
</div>
@endsection
