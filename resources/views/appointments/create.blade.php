@extends('layouts.app')
@section('title', 'Book Appointment — CityCare')
@section('page-title', 'Book Appointment')

@section('content')
<div class="page-header">
    <div>
        <h2>Book Appointment</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Book</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
<div class="card-header"><i class="bi bi-calendar-plus me-2"></i>Appointment details</div>
<div class="card-body">
<form method="POST" action="{{ route('appointments.store') }}">
    @csrf
    <div class="row g-3">

        {{-- Patient search (AJAX autocomplete) --}}
        <div class="col-12">
            <label class="form-label">Patient <span class="text-danger">*</span></label>
            <div class="position-relative">
                <input type="text" id="patient-search" class="form-control" placeholder="Type patient name or number…"
                       autocomplete="off">
                <div id="patient-results" class="autocomplete-results d-none"></div>
            </div>
            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}" required>
            <div id="selected-patient" class="mt-2 text-muted" style="font-size:13px"></div>
            @error('patient_id')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror
        </div>

        {{-- Doctor select --}}
        <div class="col-md-6">
            <label class="form-label">Doctor <span class="text-danger">*</span></label>
            <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                <option value="">— Select a doctor —</option>
                @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" @selected(old('doctor_id') == $doc->id)
                        data-fee="{{ $doc->consultation_fee }}">
                    Dr. {{ $doc->user->name }} — {{ $doc->department->name ?? '' }}
                </option>
                @endforeach
            </select>
            @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Date --}}
        <div class="col-md-6">
            <label class="form-label">Appointment date <span class="text-danger">*</span></label>
            <input type="date" name="appointment_date" id="appointment_date"
                   class="form-control @error('appointment_date') is-invalid @enderror"
                   value="{{ old('appointment_date') }}"
                   min="{{ date('Y-m-d') }}" required>
            @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Available slots (loaded via AJAX) --}}
        <div class="col-12">
            <label class="form-label">Time slot <span class="text-danger">*</span></label>
            <div id="slots-loading" class="text-muted" style="font-size:13px; display:none">
                <span class="spinner-border spinner-border-sm me-1"></span> Loading available slots…
            </div>
            <div id="slots-container" class="slots-grid"></div>
            <div id="slots-hint" class="text-muted" style="font-size:13px">
                Select a doctor and date to see available slots.
            </div>
            <input type="hidden" name="appointment_slot" id="appointment_slot" value="{{ old('appointment_slot') }}" required>
            @error('appointment_slot')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror
        </div>

        {{-- Consultation fee display --}}
        <div class="col-12" id="fee-display" style="display:none">
            <div class="p-3" style="background:var(--primary-light); border-radius:8px; font-size:13.5px">
                <i class="bi bi-info-circle me-1"></i>
                Consultation fee: <strong id="fee-value"></strong>
            </div>
        </div>

        {{-- Reason --}}
        <div class="col-12">
            <label class="form-label">Reason for visit</label>
            <textarea name="reason" class="form-control" rows="3"
                      placeholder="Brief description of symptoms or reason…">{{ old('reason') }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Book appointment</button>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── AJAX: instant patient search ──────────────────────────────────────────
const patientSearch  = document.getElementById('patient-search');
const patientResults = document.getElementById('patient-results');
const patientIdInput = document.getElementById('patient_id');
const selectedLabel  = document.getElementById('selected-patient');
let searchTimeout;

patientSearch.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { patientResults.classList.add('d-none'); return; }

    searchTimeout = setTimeout(async () => {
        const res  = await fetch(`/api/patients/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        patientResults.innerHTML = '';
        if (!data.length) {
            patientResults.innerHTML = '<div class="item text-muted">No patients found.</div>';
        } else {
            data.forEach(p => {
                const div = document.createElement('div');
                div.className = 'item';
                div.innerHTML = `<strong>${p.name}</strong> <span class="num">${p.patient_number}</span>`;
                div.addEventListener('click', () => {
                    patientIdInput.value = p.id;
                    patientSearch.value  = p.name;
                    selectedLabel.innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i>Selected: ${p.name} (${p.patient_number})`;
                    patientResults.classList.add('d-none');
                });
                patientResults.appendChild(div);
            });
        }
        patientResults.classList.remove('d-none');
    }, 300);
});

document.addEventListener('click', e => {
    if (!patientSearch.contains(e.target)) patientResults.classList.add('d-none');
});

// ── AJAX: load available slots ─────────────────────────────────────────────
const doctorSelect    = document.getElementById('doctor_id');
const dateInput       = document.getElementById('appointment_date');
const slotsContainer  = document.getElementById('slots-container');
const slotsHint       = document.getElementById('slots-hint');
const slotsLoading    = document.getElementById('slots-loading');
const slotInput       = document.getElementById('appointment_slot');
const feeDisplay      = document.getElementById('fee-display');
const feeValue        = document.getElementById('fee-value');

async function loadSlots() {
    const doctorId = doctorSelect.value;
    const date     = dateInput.value;
    if (!doctorId || !date) return;

    slotsLoading.style.display = 'block';
    slotsContainer.innerHTML   = '';
    slotsHint.style.display    = 'none';
    slotInput.value            = '';

    try {
        const res  = await fetch(`/api/available-slots?doctor_id=${doctorId}&date=${date}`);
        const data = await res.json();
        slotsLoading.style.display = 'none';

        if (data.fee) {
            feeValue.textContent    = `UGX ${Number(data.fee).toLocaleString()}`;
            feeDisplay.style.display = 'block';
        }

        if (!data.slots || data.slots.length === 0) {
            slotsContainer.innerHTML = '<p class="text-muted" style="font-size:13px">No available slots for this date.</p>';
            return;
        }

        data.slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'slot-btn';
            btn.textContent = slot;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                slotInput.value = slot;
            });
            slotsContainer.appendChild(btn);
        });
    } catch {
        slotsLoading.style.display = 'none';
        slotsContainer.innerHTML = '<p class="text-danger" style="font-size:13px">Failed to load slots. Please try again.</p>';
    }
}

doctorSelect.addEventListener('change', loadSlots);
dateInput.addEventListener('change', loadSlots);
</script>
@endpush
