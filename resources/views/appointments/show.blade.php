@extends('layouts.app')
@section('title', 'Appointment #' . $appointment->id . ' — CityCare')
@section('page-title', 'Appointment Details')

@section('content')
<div class="page-header">
    <div>
        <h2>Appointment #{{ $appointment->id }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">#{{ $appointment->id }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @if($appointment->isEditable() && auth()->user()->hasRole(['admin','receptionist','doctor']))
        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endif
        @if(auth()->user()->hasRole(['admin','cashier']) && !$appointment->payment)
        <a href="{{ route('payments.create') }}?appointment_id={{ $appointment->id }}" class="btn btn-primary btn-sm">
            <i class="bi bi-credit-card me-1"></i>Record payment
        </a>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Appointment info</div>
            <div class="card-body">
                @foreach([
                    ['Date',   $appointment->appointment_date->format('l, d M Y')],
                    ['Time',   $appointment->appointment_slot],
                    ['Status', ''],
                ] as [$label, $value])
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border); font-size:13.5px">
                    <span class="text-muted">{{ $label }}</span>
                    @if($label === 'Status')
                        <x-status-badge :status="$appointment->status" />
                    @else
                        <strong>{{ $value }}</strong>
                    @endif
                </div>
                @endforeach
                @if($appointment->reason)
                <div class="mt-3">
                    <div class="text-muted mb-1" style="font-size:12px">Reason for visit</div>
                    <div style="font-size:13.5px">{{ $appointment->reason }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Patient card --}}
        <div class="card mb-3">
            <div class="card-header">Patient</div>
            <div class="card-body" style="font-size:13.5px">
                <strong>{{ $appointment->patient->user->name ?? '—' }}</strong>
                <div class="text-muted">{{ $appointment->patient->patient_number ?? '' }}</div>
                <div class="mt-2">{{ $appointment->patient->phone ?? '' }}</div>
                <a href="{{ route('patients.show', $appointment->patient) }}" class="btn btn-outline-primary btn-sm mt-2">
                    View profile
                </a>
            </div>
        </div>

        {{-- Doctor card --}}
        <div class="card mb-3">
            <div class="card-header">Doctor</div>
            <div class="card-body" style="font-size:13.5px">
                <strong>Dr. {{ $appointment->doctor->user->name ?? '—' }}</strong>
                <div class="text-muted">{{ $appointment->doctor->specialisation ?? '' }}</div>
                <div>{{ $appointment->doctor->department->name ?? '' }}</div>
            </div>
        </div>

        {{-- Payment card --}}
        <div class="card">
            <div class="card-header">Payment</div>
            <div class="card-body" style="font-size:13.5px">
                @if($appointment->payment)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Receipt</span>
                        <strong>{{ $appointment->payment->receipt_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Amount</span>
                        <strong>UGX {{ number_format($appointment->payment->amount) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <x-status-badge :status="$appointment->payment->status" />
                    </div>
                @else
                    <p class="text-muted mb-2">No payment recorded yet.</p>
                    @if(auth()->user()->hasRole(['admin','cashier']))
                    <a href="{{ route('payments.create') }}?appointment_id={{ $appointment->id }}" class="btn btn-primary btn-sm w-100">
                        Record payment
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Clinical notes --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Clinical notes</div>
            <div class="card-body">
                @if(auth()->user()->hasRole(['admin','doctor']) && $appointment->status !== 'cancelled')
                <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                    @csrf @method('PUT')
                    {{-- Keep current date/slot/status if not changing --}}
                    <input type="hidden" name="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d') }}">
                    <input type="hidden" name="appointment_slot" value="{{ $appointment->appointment_slot }}">
                    <input type="hidden" name="status" value="{{ $appointment->status }}">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','confirmed','completed','cancelled','no_show'] as $s)
                                <option value="{{ $s }}" @selected($appointment->status === $s)>
                                    {{ ucfirst(str_replace('_',' ',$s)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Consultation notes</label>
                            <textarea name="consultation_notes" class="form-control" rows="4"
                                      placeholder="Doctor's notes from this consultation…">{{ $appointment->consultation_notes }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="3"
                                      placeholder="Diagnosis…">{{ $appointment->diagnosis }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Prescription</label>
                            <textarea name="prescription" class="form-control" rows="3"
                                      placeholder="Medications and dosage…">{{ $appointment->prescription }}</textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i>Save clinical notes
                            </button>
                        </div>
                    </div>
                </form>
                @else
                <div style="font-size:13.5px">
                    @if($appointment->consultation_notes)
                        <p class="section-title">Consultation notes</p>
                        <p>{{ $appointment->consultation_notes }}</p>
                    @endif
                    @if($appointment->diagnosis)
                        <p class="section-title">Diagnosis</p>
                        <p>{{ $appointment->diagnosis }}</p>
                    @endif
                    @if($appointment->prescription)
                        <p class="section-title">Prescription</p>
                        <p>{{ $appointment->prescription }}</p>
                    @endif
                    @if(!$appointment->consultation_notes && !$appointment->diagnosis)
                        <p class="text-muted">No clinical notes recorded yet.</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
