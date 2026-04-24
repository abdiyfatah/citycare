@extends('layouts.app')
@section('title','Payment Report — CityCare')
@section('page-title','Payment Report')
@section('content')
<div class="page-header"><div><h2>Payment Report</h2></div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3"><label class="form-label" style="font-size:12px">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-sm-3"><label class="form-label" style="font-size:12px">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-primary btn-sm">Generate</button>
            <a href="{{ request()->fullUrlWithQuery(['format'=>'csv']) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
        </div>
    </form>
</div></div>
@if($payments->count())
<div class="row g-3 mb-3">
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--success-light);color:var(--success)"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-value">UGX {{ number_format($totalRevenue) }}</div>
        <div class="stat-label">Total revenue</div>
        <div class="stat-accent" style="background:var(--success)"></div>
    </div></div>
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--info-light);color:var(--info)"><i class="bi bi-receipt"></i></div>
        <div class="stat-value">{{ $payments->count() }}</div>
        <div class="stat-label">Transactions</div>
        <div class="stat-accent" style="background:var(--info)"></div>
    </div></div>
    <div class="col-sm-4"><div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-light);color:var(--primary)"><i class="bi bi-calculator"></i></div>
        <div class="stat-value">UGX {{ number_format($payments->count() ? $totalRevenue / $payments->count() : 0) }}</div>
        <div class="stat-label">Average per visit</div>
        <div class="stat-accent" style="background:var(--primary)"></div>
    </div></div>
</div>
@endif
<div class="card"><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Receipt</th><th>Date</th><th>Patient</th><th>Doctor</th><th>Method</th><th>Amount</th></tr></thead>
    <tbody>
    @forelse($payments as $pay)
    <tr>
        <td style="font-family:monospace;font-size:12px">{{ $pay->receipt_number }}</td>
        <td style="font-size:13px">{{ $pay->paid_at?->format('d M Y') }}</td>
        <td>{{ $pay->appointment->patient->user->name??'—' }}</td>
        <td>Dr. {{ $pay->appointment->doctor->user->name??'—' }}</td>
        <td><span class="badge badge-confirmed">{{ ucfirst(str_replace('_',' ',$pay->payment_method)) }}</span></td>
        <td><strong style="color:var(--success)">UGX {{ number_format($pay->amount) }}</strong></td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center py-4 text-muted">No results. Set date range and click Generate.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div></div>
@endsection
