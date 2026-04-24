@extends('layouts.app')
@section('title','Doctor Schedule — CityCare')
@section('page-title','Doctor Schedule')
@section('content')
<div class="page-header"><div><h2>Doctor Schedule</h2></div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
            <select name="doctor_id" class="form-select form-select-sm" required>
                <option value="">— Select doctor —</option>
                @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" @selected(request('doctor_id')==$doc->id)>Dr. {{ $doc->user->name }}</option>
                @endforeach
            </select></div>
        <div class="col-sm-3"><input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', date('Y-m-d')) }}"></div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-primary btn-sm">View schedule</button>
            @if(request('doctor_id') && request('date'))
            <a href="{{ request()->fullUrlWithQuery(['format'=>'csv']) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
            @endif
        </div>
    </form>
</div></div>
@isset($doctor)
<div class="card"><div class="card-header">
    Dr. {{ $doctor->user->name }} — {{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('l, d M Y') : '' }}
</div><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Slot</th><th>Patient</th><th>Reason</th><th>Status</th><th>Payment</th></tr></thead>
    <tbody>
    @forelse($appointments as $appt)
    <tr>
        <td><strong>{{ $appt->appointment_slot }}</strong></td>
        <td>{{ $appt->patient->user->name??'—' }}<div style="font-size:11px;color:var(--text-muted)">{{ $appt->patient->patient_number }}</div></td>
        <td style="font-size:12px;color:var(--text-muted)">{{ Str::limit($appt->reason,40) }}</td>
        <td><x-status-badge :status="$appt->status" /></td>
        <td>@if($appt->payment && $appt->payment->status==='paid')
            <span style="color:var(--success);font-size:12px;font-weight:600">Paid</span>
            @else<span style="color:var(--text-muted);font-size:12px">—</span>@endif</td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center py-4 text-muted">No appointments for this date.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div></div>
@endisset
@endsection
