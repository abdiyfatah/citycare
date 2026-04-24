@extends('layouts.app')
@section('title','Appointment Report — CityCare')
@section('page-title','Appointment Report')
@section('content')
<div class="page-header"><div><h2>Appointment Report</h2></div>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3"><label class="form-label" style="font-size:12px">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-sm-3"><label class="form-label" style="font-size:12px">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        <div class="col-sm-2"><label class="form-label" style="font-size:12px">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(['pending','confirmed','completed','cancelled','no_show'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select></div>
        <div class="col-sm-2"><label class="form-label" style="font-size:12px">Doctor</label>
            <select name="doctor_id" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" @selected(request('doctor_id')==$doc->id)>Dr. {{ $doc->user->name }}</option>
                @endforeach
            </select></div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-primary btn-sm">Generate</button>
            <a href="{{ request()->fullUrlWithQuery(['format'=>'csv']) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-1"></i>CSV</a>
        </div>
    </form>
</div></div>
<div class="card"><div class="card-header d-flex justify-content-between">
    <span>Results: <strong>{{ $appointments->count() }}</strong> appointments</span>
</div><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Date</th><th>Slot</th><th>Patient</th><th>Doctor</th><th>Reason</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($appointments as $appt)
    <tr>
        <td>{{ $appt->appointment_date->format('d M Y') }}</td>
        <td>{{ $appt->appointment_slot }}</td>
        <td>{{ $appt->patient->user->name??'—' }}</td>
        <td>Dr. {{ $appt->doctor->user->name??'—' }}</td>
        <td style="font-size:12px;color:var(--text-muted)">{{ Str::limit($appt->reason,40) }}</td>
        <td><x-status-badge :status="$appt->status" /></td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center py-4 text-muted">No results. Apply filters above and click Generate.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div></div>
@endsection
