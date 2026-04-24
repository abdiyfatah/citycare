@extends('layouts.app')
@section('title','Add Doctor — CityCare')
@section('page-title','Add Doctor')
@section('content')
<div class="page-header">
    <div><h2>Add new doctor</h2></div>
</div>
<div class="row justify-content-center"><div class="col-lg-9">
<div class="card"><div class="card-header"><i class="bi bi-person-badge me-2"></i>Doctor information</div>
<div class="card-body">
<form method="POST" action="{{ route('doctors.store') }}">
@csrf
<div class="row g-3">
    <div class="col-12"><p class="section-title">Account credentials</p></div>
    <div class="col-md-6"><label class="form-label">Full name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Password *</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-md-6"><label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
    <div class="col-12 mt-2"><p class="section-title">Professional details</p></div>
    <div class="col-md-6"><label class="form-label">Department *</label>
        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
            <option value="">— Select —</option>
            @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @selected(old('department_id')==$dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Specialisation *</label>
        <input type="text" name="specialisation" class="form-control @error('specialisation') is-invalid @enderror" value="{{ old('specialisation') }}" required>
        @error('specialisation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6"><label class="form-label">Qualification</label>
        <input type="text" name="qualification" class="form-control" value="{{ old('qualification') }}" placeholder="e.g. MBChB, MMed">
    </div>
    <div class="col-md-3"><label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
    </div>
    <div class="col-md-3"><label class="form-label">Consultation fee (UGX)</label>
        <input type="number" name="consultation_fee" class="form-control" value="{{ old('consultation_fee', 50000) }}" min="0">
    </div>
    <div class="col-12"><label class="form-label">Bio / About</label>
        <textarea name="bio" class="form-control" rows="3">{{ old('bio') }}</textarea>
    </div>
    <div class="col-12 mt-2"><p class="section-title">Consultation schedule</p>
        <p class="text-muted" style="font-size:12.5px">Select available days. Slots are auto-set to 30-min intervals 08:00–17:00. You can customise after saving.</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="schedule_days[]" value="{{ $day }}" id="day_{{ $day }}"
                       @checked(in_array($day, ['monday','tuesday','wednesday','thursday','friday'])) >
                <label class="form-check-label" for="day_{{ $day }}">{{ ucfirst($day) }}</label>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save doctor</button>
    </div>
</div>
</form>
</div></div>
</div></div>
@endsection
