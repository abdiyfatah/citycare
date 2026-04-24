@extends('layouts.app')
@section('title', 'Edit Appointment')
@section('page-title', 'Edit Appointment')
@section('content')
<div class="page-header">
    <div>
        <h2>Edit appointment #{{ $appointment->id }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center"><div class="col-lg-8">
<div class="card"><div class="card-header"><i class="bi bi-pencil me-2"></i>Update appointment</div>
<div class="card-body">
<form method="POST" action="{{ route('appointments.update', $appointment) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Patient</label>
        <input class="form-control" value="{{ $appointment->patient->user->name }} ({{ $appointment->patient->patient_number }})" readonly>
    </div>
    <div class="col-md-6">
        <label class="form-label">Doctor</label>
        <input class="form-control" value="Dr. {{ $appointment->doctor->user->name }}" readonly>
    </div>
    <div class="col-md-6">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="appointment_date" id="appointment_date" class="form-control"
               value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" required>
        @error('appointment_date')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            @foreach(['pending','confirmed','completed','cancelled','no_show'] as $s)
            <option value="{{ $s }}" @selected(old('status',$appointment->status)===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Time slot <span class="text-danger">*</span></label>
        <div id="slots-loading" style="display:none;font-size:13px" class="text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Loading…</div>
        <div id="slots-container" class="slots-grid"></div>
        <input type="hidden" name="appointment_slot" id="appointment_slot" value="{{ old('appointment_slot',$appointment->appointment_slot) }}" required>
        @error('appointment_slot')<div class="text-danger mt-1" style="font-size:12px">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Reason for visit</label>
        <textarea name="reason" class="form-control" rows="2">{{ old('reason',$appointment->reason) }}</textarea>
    </div>
    @if(auth()->user()->hasRole(['admin','doctor']))
    <div class="col-12"><hr><p class="section-title">Clinical notes</p></div>
    <div class="col-12">
        <label class="form-label">Consultation notes</label>
        <textarea name="consultation_notes" class="form-control" rows="3">{{ old('consultation_notes',$appointment->consultation_notes) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Diagnosis</label>
        <textarea name="diagnosis" class="form-control" rows="2">{{ old('diagnosis',$appointment->diagnosis) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Prescription</label>
        <textarea name="prescription" class="form-control" rows="2">{{ old('prescription',$appointment->prescription) }}</textarea>
    </div>
    @endif
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save changes</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
@push('scripts')
<script>
const doctorId = {{ $appointment->doctor_id }};
const dateInput = document.getElementById('appointment_date');
const container = document.getElementById('slots-container');
const loading   = document.getElementById('slots-loading');
const slotInput = document.getElementById('appointment_slot');
const currentSlot = '{{ $appointment->appointment_slot }}';

async function loadSlots() {
    const date = dateInput.value;
    if (!date) return;
    loading.style.display = 'block';
    container.innerHTML = '';
    const res  = await fetch(`/api/available-slots?doctor_id=${doctorId}&date=${date}`);
    const data = await res.json();
    loading.style.display = 'none';
    let slots = data.slots || [];
    if (!slots.includes(currentSlot)) slots.unshift(currentSlot);
    slots.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-btn' + (slot === slotInput.value ? ' selected' : '');
        btn.textContent = slot + (slot === currentSlot ? ' ★' : '');
        btn.addEventListener('click', () => {
            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            slotInput.value = slot;
        });
        container.appendChild(btn);
    });
}
dateInput.addEventListener('change', loadSlots);
loadSlots();
</script>
@endpush
