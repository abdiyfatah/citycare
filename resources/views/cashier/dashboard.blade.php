@extends('layouts.app')
@section('title','Cashier Dashboard — CityCare')
@section('page-title','Cashier Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6"><div class="stat-card">
        <div class="stat-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-cash-coin"></i></div>
        <div class="stat-value">UGX {{ number_format($todayRevenue) }}</div>
        <div class="stat-label">Today's revenue</div>
        <div class="stat-accent" style="background:var(--success)"></div>
    </div></div>
    <div class="col-sm-6"><div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-light);color:var(--warning)"><i class="bi bi-hourglass"></i></div>
        <div class="stat-value">{{ $pendingPayments->count() }}</div>
        <div class="stat-label">Awaiting payment</div>
        <div class="stat-accent" style="background:var(--warning)"></div>
    </div></div>
</div>
<div class="row g-3">
    <div class="col-lg-7"><div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Awaiting payment</span>
            <a href="{{ route('payments.create') }}" class="btn btn-sm btn-primary">Record payment</a>
        </div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Fee</th><th></th></tr></thead>
            <tbody>
            @forelse($pendingPayments as $appt)
            <tr>
                <td>{{ $appt->patient->user->name??'—' }}</td>
                <td>Dr. {{ $appt->doctor->user->name??'—' }}</td>
                <td style="font-size:12px">{{ $appt->appointment_date->format('d M Y') }}</td>
                <td><strong>UGX {{ number_format($appt->doctor->consultation_fee) }}</strong></td>
                <td><a href="{{ route('payments.create') }}?appointment_id={{ $appt->id }}" class="btn btn-sm btn-primary">Pay</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-3 text-muted">All caught up!</td></tr>
            @endforelse
            </tbody>
        </table></div></div>
    </div></div>
    <div class="col-lg-5"><div class="card">
        <div class="card-header">Recent payments</div><div class="card-body p-0">
        @forelse($recentPayments as $pay)
        <div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--border)">
            <div>
                <div style="font-size:13px;font-weight:500">{{ $pay->appointment->patient->user->name??'—' }}</div>
                <div style="font-size:11px;color:var(--text-muted)">{{ $pay->receipt_number }} · {{ $pay->paid_at?->format('H:i') }}</div>
            </div>
            <strong style="color:var(--success)">UGX {{ number_format($pay->amount) }}</strong>
        </div>
        @empty
        <p class="text-muted text-center py-3" style="font-size:13px">No payments today.</p>
        @endforelse
        </div>
    </div></div>
</div>
@endsection
