@extends('layouts.app')
@section('title','Edit Department — CityCare')
@section('page-title','Edit Department')
@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card"><div class="card-header">Edit department</div><div class="card-body">
<form method="POST" action="{{ route('departments.update', $department) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-12"><label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$department->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12"><label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description',$department->description) }}</textarea>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($department->is_active)>
            <label class="form-check-label">Active</label>
        </div>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
