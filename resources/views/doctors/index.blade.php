@extends('layouts.app')
@section('title', 'Doctors — CityCare')
@section('page-title', 'Doctors')
@section('content')
<div class="page-header">
    <div><h2>Doctors</h2></div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('doctors.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Add doctor</a>
    @endif
</div>
<div class="card mb-3"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Name or specialisation…" value="{{ request('search') }}"></div>
        <div class="col-sm-3">
            <select name="department_id" class="form-select form-select-sm">
                <option value="">All departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id')==$dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
        </div>
    </form>
</div></div>
<div class="row g-3">
@forelse($doctors as $doctor)
<div class="col-md-6 col-xl-4">
<div class="card h-100"><div class="card-body d-flex gap-3">
    <div class="avatar-lg flex-shrink-0">{{ strtoupper(substr($doctor->user->name??'D',0,1)) }}</div>
    <div class="flex-grow-1">
        <h6 class="mb-0" style="font-weight:600">{{ $doctor->user->name??'—' }}</h6>
        <div style="font-size:12px;color:var(--text-muted)">{{ $doctor->specialisation }}</div>
        <div style="font-size:12px;color:var(--primary)">{{ $doctor->department->name??'—' }}</div>
        <div style="font-size:12px;margin-top:4px;color:var(--text-muted)">{{ $doctor->qualification }}</div>
        <div class="d-flex align-items-center gap-2 mt-2">
            @if($doctor->is_active)<span class="badge badge-completed">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif
            <span style="font-size:12px;color:var(--success);font-weight:600">UGX {{ number_format($doctor->consultation_fee) }}</span>
        </div>
        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-primary">View</a>
            @if(auth()->user()->isAdmin())<a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-secondary">Edit</a>@endif
        </div>
    </div>
</div></div>
</div>
@empty
<div class="col-12"><p class="text-center text-muted py-4">No doctors found.</p></div>
@endforelse
</div>
@if($doctors->hasPages())<div class="mt-3">{{ $doctors->links() }}</div>@endif
@endsection
