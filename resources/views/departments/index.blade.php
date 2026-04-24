@extends('layouts.app')
@section('title','Departments — CityCare')
@section('page-title','Departments')
@section('content')
<div class="page-header">
    <div><h2>Departments</h2></div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('departments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add department</a>
    @endif
</div>
<div class="card"><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0">
    <thead><tr><th>Name</th><th>Description</th><th>Doctors</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($departments as $dept)
    <tr>
        <td><strong>{{ $dept->name }}</strong></td>
        <td style="font-size:13px;color:var(--text-muted)">{{ Str::limit($dept->description, 60) }}</td>
        <td><span class="badge" style="background:var(--primary-light);color:var(--primary)">{{ $dept->doctors_count }}</span></td>
        <td>@if($dept->is_active)<span class="badge badge-completed">Active</span>@else<span class="badge badge-cancelled">Inactive</span>@endif</td>
        <td class="d-flex gap-1">
            <a href="{{ route('departments.show', $dept) }}" class="btn btn-sm btn-outline-primary">View</a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="{{ route('departments.destroy', $dept) }}" class="d-inline"
                  onsubmit="return confirm('Delete this department?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center py-4 text-muted">No departments yet.</td></tr>
    @endforelse
    </tbody>
</table></div>
</div></div>
@endsection
