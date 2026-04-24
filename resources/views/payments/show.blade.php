@extends('layouts.app')
@section('title','Receipt — CityCare')
@section('page-title','Payment Receipt')
@section('content')
<div class="page-header">
    <div><h2>Receipt: {{ $payment->receipt_number }}</h2></div>
    @if(auth()->user()->hasRole(['admin','cashier']))
    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endif
</div>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-body p-4">
        {{-- Receipt header --}}
        <div class="text-center mb-4 pb-3" style="border-bottom:2px dashed var(--border)">
            <i class="bi bi-heart-pulse-fill" style="font-size:28px;color:var(--primary)"></i>
            <h4 style="font-family:'Playfair Display',serif;margin-top:4px">CityCare Medical Centre</h4>
            <div class="text-muted" style="font-size:13px">Payment receipt</div>
        </div>
        @foreach([
            ['Receipt #',       $payment->receipt_number],
            ['Date',            $payment->paid_at?->format('d M Y H:i') ?? '—'],
            ['Patient',         $payment->appointment->patient->user->name??'—'],
            ['Doctor',          'Dr. '.($payment->appointment->doctor->user->name??'—')],
            ['Department',      $payment->appointment->doctor->department->name??'—'],
            ['Appointment date',$payment->appointment->appointment_date->format('d M Y').' at '.$payment->appointment->appointment_slot],
            ['Payment method',  ucfirst(str_replace('_',' ',$payment->payment_method))],
        ] as [$lbl,$val])
        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:13.5px">
            <span class="text-muted">{{ $lbl }}</span><strong>{{ $val }}</strong>
        </div>
        @endforeach
        <div class="d-flex justify-content-between py-3 mt-1" style="background:var(--primary-light);border-radius:8px;padding-left:12px;padding-right:12px">
            <span style="font-weight:600;font-size:15px">Amount paid</span>
            <strong style="font-size:18px;color:var(--primary)">UGX {{ number_format($payment->amount) }}</strong>
        </div>
        <div class="text-center mt-3"><x-status-badge :status="$payment->status" /></div>
        @if($payment->notes)<p class="text-muted mt-3" style="font-size:13px">Note: {{ $payment->notes }}</p>@endif
    </div>
</div>
</div></div>
@endsection
