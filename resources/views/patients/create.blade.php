@extends('layouts.app')
@section('title', 'Register Patient — CityCare')
@section('page-title', 'Register Patient')

@section('content')
<div class="page-header">
    <div>
        <h2>Register new patient</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
            <li class="breadcrumb-item active">Register</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
<div class="card-header"><i class="bi bi-person-plus me-2"></i>Patient information</div>
<div class="card-body">
<form method="POST" action="{{ route('patients.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-12"><p class="section-title">Account credentials</p></div>

        <div class="col-md-6">
            <label class="form-label">Full name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email address <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="col-12 mt-2"><p class="section-title">Personal details</p></div>

        <div class="col-md-4">
            <label class="form-label">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">— Select —</option>
                @foreach(['male','female','other'] as $g)
                <option value="{{ $g }}" @selected(old('gender') === $g)>{{ ucfirst($g) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Blood group</label>
            <select name="blood_group" class="form-select">
                <option value="">— Select —</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" @selected(old('blood_group') === $bg)>{{ $bg }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone number</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
        </div>

        <div class="col-12 mt-2"><p class="section-title">Emergency contact</p></div>
        <div class="col-md-6">
            <label class="form-label">Emergency contact name</label>
            <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Emergency contact phone</label>
            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
        </div>

        <div class="col-12 mt-2"><p class="section-title">Medical information</p></div>
        <div class="col-12">
            <label class="form-label">Known allergies</label>
            <input type="text" name="allergies" class="form-control" value="{{ old('allergies') }}" placeholder="e.g. Penicillin, pollen">
        </div>
        <div class="col-12">
            <label class="form-label">Medical notes</label>
            <textarea name="medical_notes" class="form-control" rows="3">{{ old('medical_notes') }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end mt-2">
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-person-check me-1"></i>Register patient</button>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
@endsection
