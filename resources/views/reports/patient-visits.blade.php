@extends('layouts.app')
@section('title','Patient Visits — CityCare')
@section('page-title','Patient Visit History')
@section('content')
<div class="page-header"><div><h2>Patient Visit History</h2></div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5">
            <select name="patient_id" class="form-select form-select-sm" required>
                <option value="">— Select patient —</option>
                @foreach($patients as $pat)
                <option value="{{ $pat->id }}" @selected(request('patient_id')==$pat->id)>{{ $pat->user->name }} ({{ $pat->patient_number }})</option>
                @endforeach
            </select></div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-primary btn-sm">View history</button>
            @if(request('patient_id'))
            <a href="{{ request()->fullUrlWithQuery(['format'=>'csv']) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
            @endif
        </div>
    </form>
</div></div>
@isset($patient)
<div class="card"><div class="card-header">
    {{ $patient->user->name }} — {{ $patient->patient_number }} — {{ $visits->count() }} visits
</div><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Date</th><th>Doctor</th><th>Status</th><th>Diagnosis</th><th>Payment</th></tr></thead>
    <tbody>
    @forelse($visits as $v)
    <tr>
        <td>{{ $v->appointment_date->format('d M Y') }}<div style="font-size:11px;color:var(--text-muted)">{{ $v->appointment_slot }}</div></td>
        <td>Dr. {{ $v->doctor->user->name??'—' }}</td>
        <td><x-status-badge :status="$v->status" /></td>
        <td style="font-size:13px;color:var(--text-muted)">{{ Str::limit($v->diagnosis,50) ?? '—' }}</td>
        <td>@if($v->payment && $v->payment->status==='paid')
            <span style="color:var(--success);font-size:12px;font-weight:600">UGX {{ number_format($v->payment->amount) }}</span>
            @else<span style="font-size:12px;color:var(--text-muted)">Unpaid</span>@endif</td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center py-4 text-muted">No visit records found.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div></div>
@endisset
@endsection
