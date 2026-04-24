@extends('layouts.app')
@section('title','Doctor Profile — CityCare')
@section('page-title','Doctor Profile')
@section('content')
<div class="page-header">
    <div><h2>Dr. {{ $doctor->user->name }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('doctors.index') }}">Doctors</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol></nav>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endif
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-body text-center py-4">
            <div class="avatar-lg mx-auto mb-3" style="width:72px;height:72px;font-size:28px">{{ strtoupper(substr($doctor->user->name??'D',0,1)) }}</div>
            <h5>{{ $doctor->user->name }}</h5>
            <div class="text-muted" style="font-size:13px">{{ $doctor->user->email }}</div>
            <span class="badge mt-1" style="background:var(--primary-light);color:var(--primary)">{{ $doctor->specialisation }}</span>
        </div>
        <div class="card-body pt-0">
            @foreach([
                ['Department',   $doctor->department->name??'—'],
                ['Qualification',$doctor->qualification??'—'],
                ['Phone',        $doctor->phone??'—'],
                ['Consult fee',  'UGX '.number_format($doctor->consultation_fee)],
                ['Status',       $doctor->is_active?'Active':'Inactive'],
            ] as [$lbl,$val])
            <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border);font-size:13.5px">
                <span class="text-muted">{{ $lbl }}</span><strong>{{ $val }}</strong>
            </div>
            @endforeach
            @if($doctor->bio)<div class="mt-3" style="font-size:13px;color:var(--text-muted)">{{ $doctor->bio }}</div>@endif
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header">Recent appointments</div>
        <div class="card-body p-0">
        <div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Date</th><th>Patient</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($appointments as $appt)
            <tr>
                <td><strong>{{ $appt->appointment_date->format('d M Y') }}</strong>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $appt->appointment_slot }}</div></td>
                <td>{{ $appt->patient->user->name??'—' }}</td>
                <td><x-status-badge :status="$appt->status" /></td>
                <td><a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-3 text-muted">No appointments yet.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        </div>
        @if($appointments->hasPages())<div class="card-body py-2 border-top">{{ $appointments->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
