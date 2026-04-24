@extends('layouts.app')
@section('title', 'Patients — CityCare')
@section('page-title', 'Patients')

@section('content')
<div class="page-header">
    <div>
        <h2>Patients</h2>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item active">Patients</li>
        </ol></nav>
    </div>
    @if(auth()->user()->hasRole(['admin','receptionist']))
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Register patient
    </a>
    @endif
</div>

{{-- Search & filter --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5 col-lg-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Name, email, number, phone…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="gender" class="form-select form-select-sm">
                    <option value="">All genders</option>
                    <option value="male"   @selected(request('gender') === 'male')>Male</option>
                    <option value="female" @selected(request('gender') === 'female')>Female</option>
                    <option value="other"  @selected(request('gender') === 'other')>Other</option>
                </select>
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="blood_group" class="form-select form-select-sm">
                    <option value="">Blood group</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option @selected(request('blood_group') === $bg)>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr>
                    <th>Patient #</th><th>Name</th><th>Gender</th><th>Age</th>
                    <th>Phone</th><th>Blood group</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td><span class="badge" style="background:var(--primary-light); color:var(--primary); font-weight:600">
                        {{ $patient->patient_number }}
                    </span></td>
                    <td>
                        <strong>{{ $patient->user->name }}</strong>
                        <div style="font-size:11px; color:var(--text-muted)">{{ $patient->user->email }}</div>
                    </td>
                    <td>{{ ucfirst($patient->gender ?? '—') }}</td>
                    <td>{{ $patient->age ?? '—' }} yrs</td>
                    <td>{{ $patient->phone ?? '—' }}</td>
                    <td>{{ $patient->blood_group ?? '—' }}</td>
                    <td>
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if(auth()->user()->hasRole(['admin','receptionist']))
                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary ms-1">Edit</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No patients found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($patients->hasPages())
    <div class="card-body py-2 border-top">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
