@extends('layouts.app')
@section('title','Edit Payment — CityCare')
@section('page-title','Edit Payment')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card"><div class="card-header">Edit payment — {{ $payment->receipt_number }}</div>
<div class="card-body">
<form method="POST" action="{{ route('payments.update', $payment) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-12">
        <div class="p-3 mb-2" style="background:var(--bg);border-radius:8px;font-size:13.5px">
            Patient: <strong>{{ $payment->appointment->patient->user->name??'—' }}</strong>
        </div>
    </div>
    <div class="col-md-6"><label class="form-label">Amount (UGX)</label>
        <input type="number" name="amount" class="form-control" value="{{ old('amount',$payment->amount) }}" min="0" step="500" required>
    </div>
    <div class="col-md-6"><label class="form-label">Payment method</label>
        <select name="payment_method" class="form-select">
            @foreach(['cash','card','mobile_money','insurance'] as $m)
            <option value="{{ $m }}" @selected(old('payment_method',$payment->payment_method)===$m)>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12"><label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['pending','paid','refunded'] as $s)
            <option value="{{ $s }}" @selected(old('status',$payment->status)===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12"><label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes',$payment->notes) }}</textarea>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save changes</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
