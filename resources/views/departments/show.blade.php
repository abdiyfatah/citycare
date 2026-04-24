@extends('layouts.app')
@section('title','Department — CityCare')
@section('page-title','Department')
@section('content')
<div class="page-header">
    <div><h2>{{ $department->name }}</h2></div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('departments.edit', $department) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endif
</div>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <p style="color:var(--text-muted);font-size:13.5px">{{ $department->description ?? 'No description.' }}</p>
            <div class="d-flex justify-content-between py-2" style="border-top:1px solid var(--border);font-size:13.5px">
                <span class="text-muted">Status</span>
                @if($department->is_active)<span class="badge badge-completed">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif
            </div>
            <div class="d-flex justify-content-between py-2" style="border-top:1px solid var(--border);font-size:13.5px">
                <span class="text-muted">Doctors</span><strong>{{ $department->doctors->count() }}</strong>
            </div>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header">Doctors in this department</div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
            <thead><tr><th>Name</th><th>Specialisation</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($department->doctors as $doc)
            <tr>
                <td><strong>{{ $doc->user->name??'—' }}</strong></td>
                <td style="font-size:13px">{{ $doc->specialisation }}</td>
                <td>@if($doc->is_active)<span class="badge badge-completed">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif</td>
                <td><a href="{{ route('doctors.show', $doc) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-3 text-muted">No doctors in this department.</td></tr>
            @endforelse
            </tbody>
        </table></div></div>
        </div>
    </div>
</div>
@endsection
