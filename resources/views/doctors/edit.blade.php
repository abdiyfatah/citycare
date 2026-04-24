@extends('layouts.app')
@section('title','Edit Doctor — CityCare')
@section('page-title','Edit Doctor')
@section('content')
<div class="page-header"><div><h2>Edit: {{ $doctor->user->name }}</h2></div></div>
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card"><div class="card-header"><i class="bi bi-pencil me-2"></i>Update doctor profile</div>
<div class="card-body">
<form method="POST" action="{{ route('doctors.update', $doctor) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Full name *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name',$doctor->user->name) }}" required>
    </div>
    <div class="col-md-6"><label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" value="{{ old('email',$doctor->user->email) }}" required>
    </div>
    <div class="col-md-6"><label class="form-label">Department *</label>
        <select name="department_id" class="form-select" required>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @selected(old('department_id',$doctor->department_id)==$dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6"><label class="form-label">Specialisation *</label>
        <input type="text" name="specialisation" class="form-control" value="{{ old('specialisation',$doctor->specialisation) }}" required>
    </div>
    <div class="col-md-6"><label class="form-label">Qualification</label>
        <input type="text" name="qualification" class="form-control" value="{{ old('qualification',$doctor->qualification) }}">
    </div>
    <div class="col-md-3"><label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone',$doctor->phone) }}">
    </div>
    <div class="col-md-3"><label class="form-label">Consultation fee (UGX)</label>
        <input type="number" name="consultation_fee" class="form-control" value="{{ old('consultation_fee',$doctor->consultation_fee) }}" min="0">
    </div>
    <div class="col-12"><label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="3">{{ old('bio',$doctor->bio) }}</textarea>
    </div>
    <div class="col-md-6">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active',$doctor->is_active))>
            <label class="form-check-label" for="is_active">Doctor is active</label>
        </div>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save changes</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
