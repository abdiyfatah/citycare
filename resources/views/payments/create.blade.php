@extends('layouts.app')
@section('title','Record Payment — CityCare')
@section('page-title','Record Payment')
@section('content')
<div class="page-header"><div><h2>Record payment</h2></div></div>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card"><div class="card-header"><i class="bi bi-credit-card me-2"></i>Payment details</div>
<div class="card-body">
<form method="POST" action="{{ route('payments.store') }}">
@csrf
<div class="row g-3">
    <div class="col-12"><label class="form-label">Appointment *</label>
        <select name="appointment_id" class="form-select @error('appointment_id') is-invalid @enderror" required>
            <option value="">— Select appointment —</option>
            @if($appointment)
            <option value="{{ $appointment->id }}" selected>
                #{{ $appointment->id }} — {{ $appointment->patient->user->name }} — {{ $appointment->appointment_date->format('d M Y') }} {{ $appointment->appointment_slot }}
            </option>
            @endif
            @foreach($pendingAppointments as $apt)
            @if(!$appointment || $apt->id !== $appointment->id)
            <option value="{{ $apt->id }}" @selected(old('appointment_id')==$apt->id)>
                #{{ $apt->id }} — {{ $apt->patient->user->name??'?' }} — {{ $apt->appointment_date->format('d M Y') }} {{ $apt->appointment_slot }}
            </option>
            @endif
            @endforeach
        </select>
        @error('appointment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Amount (UGX) *</label>
        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
               value="{{ old('amount', $appointment?->doctor?->consultation_fee ?? '') }}" min="0" step="500" required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Payment method *</label>
        <select name="payment_method" class="form-select" required>
            @foreach(['cash','card','mobile_money','insurance'] as $m)
            <option value="{{ $m }}" @selected(old('payment_method')===$m)>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12"><label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Record payment</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
