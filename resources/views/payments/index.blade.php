@extends('layouts.app')
@section('title','Payments — CityCare')
@section('page-title','Payments')
@section('content')
<div class="page-header">
    <div><h2>Payments</h2></div>
    @if(auth()->user()->hasRole(['admin','cashier']))
    <a href="{{ route('payments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Record payment</a>
    @endif
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Receipt # or patient…" value="{{ request('search') }}"></div>
        <div class="col-sm-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All statuses</option>
                @foreach(['pending','paid','refunded'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <select name="method" class="form-select form-select-sm">
                <option value="">All methods</option>
                @foreach(['cash','card','mobile_money','insurance'] as $m)
                <option value="{{ $m }}" @selected(request('method')===$m)>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2"><input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}"></div>
        <div class="col-auto">
            <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
        </div>
    </form>
</div></div>
<div class="card"><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Receipt</th><th>Patient</th><th>Doctor</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($payments as $pay)
    <tr>
        <td><strong style="font-family:monospace;font-size:12px">{{ $pay->receipt_number }}</strong></td>
        <td style="font-size:13px">{{ $pay->appointment->patient->user->name??'—' }}</td>
        <td style="font-size:13px">{{ $pay->appointment->doctor->user->name??'—' }}</td>
        <td><strong style="color:var(--success)">UGX {{ number_format($pay->amount) }}</strong></td>
        <td><span class="badge badge-confirmed">{{ ucfirst(str_replace('_',' ',$pay->payment_method)) }}</span></td>
        <td style="font-size:12px">{{ $pay->paid_at?->format('d M Y') ?? '—' }}</td>
        <td><x-status-badge :status="$pay->status" /></td>
        <td>
            <a href="{{ route('payments.show', $pay) }}" class="btn btn-sm btn-outline-primary">View</a>
            @if(auth()->user()->hasRole(['admin','cashier']))
            <a href="{{ route('payments.edit', $pay) }}" class="btn btn-sm btn-outline-secondary ms-1">Edit</a>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center py-4 text-muted">No payments found.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div>
@if($payments->hasPages())<div class="card-body py-2 border-top">{{ $payments->links() }}</div>@endif
</div>
@endsection
