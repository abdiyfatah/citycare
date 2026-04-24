@extends('layouts.app')
@section('title', 'Edit Patient — CityCare')
@section('page-title', 'Edit Patient')

@section('content')
<div class="page-header">
    <div>
        <h2>Edit: {{ $patient->user->name }}</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
            <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->user->name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card">
<div class="card-header"><i class="bi bi-pencil me-2"></i>Update patient record</div>
<div class="card-body">
<form method="POST" action="{{ route('patients.update', $patient) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12"><p class="section-title">Account details</p></div>
        <div class="col-md-6">
            <label class="form-label">Full name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $patient->user->name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $patient->user->email) }}" required>
        </div>

        <div class="col-12 mt-2"><p class="section-title">Personal details</p></div>
        <div class="col-md-4">
            <label class="form-label">Date of birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">— Select —</option>
                @foreach(['male','female','other'] as $g)
                <option value="{{ $g }}" @selected(old('gender', $patient->gender) === $g)>{{ ucfirst($g) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Blood group</label>
            <select name="blood_group" class="form-select">
                <option value="">— Select —</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" @selected(old('blood_group', $patient->blood_group) === $bg)>{{ $bg }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $patient->address) }}">
        </div>

        <div class="col-12 mt-2"><p class="section-title">Emergency contact</p></div>
        <div class="col-md-6">
            <label class="form-label">Emergency contact name</label>
            <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Emergency contact phone</label>
            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
        </div>

        <div class="col-12 mt-2"><p class="section-title">Medical information</p></div>
        <div class="col-12">
            <label class="form-label">Known allergies</label>
            <input type="text" name="allergies" class="form-control" value="{{ old('allergies', $patient->allergies) }}">
        </div>
        <div class="col-12">
            <label class="form-label">Medical notes</label>
            <textarea name="medical_notes" class="form-control" rows="3">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save changes</button>
        </div>

        {{-- Delete (admin only) --}}
        @if(auth()->user()->isAdmin())
        <div class="col-12">
            <hr>
            <div class="d-flex align-items-center justify-content-between p-3" style="background:var(--danger-light); border-radius:8px">
                <div>
                    <strong style="color:var(--danger)">Delete patient record</strong>
                    <p class="mb-0" style="font-size:12.5px; color:var(--danger)">This action is irreversible and will remove all associated data.</p>
                </div>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
            </div>
        </div>
        @endif
    </div>
</form>
</div>
</div>
</div>
</div>

{{-- Delete confirmation modal --}}
@if(auth()->user()->isAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Confirm deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                Are you sure you want to permanently delete <strong>{{ $patient->user->name }}</strong>?
                All appointments and data will be removed.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('patients.destroy', $patient) }}" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
