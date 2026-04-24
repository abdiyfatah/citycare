<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /** List patients with search, filter, and pagination */
    public function index(Request $request)
    {
        $query = Patient::with('user')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                   ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhere('patient_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->gender, fn($q, $g) => $q->where('gender', $g))
            ->when($request->blood_group, fn($q, $bg) => $q->where('blood_group', $bg))
            ->latest();

        $patients = $query->paginate(15)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|unique:users,email',
            'password'                => 'required|min:8|confirmed',
            'date_of_birth'           => 'nullable|date|before:today',
            'gender'                  => 'nullable|in:male,female,other',
            'phone'                   => 'nullable|string|max:20',
            'address'                 => 'nullable|string',
            'blood_group'             => 'nullable|string|max:10',
            'allergies'               => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'patient',
            ]);

            Patient::create([
                'user_id'                 => $user->id,
                'patient_number'          => Patient::generatePatientNumber(),
                'date_of_birth'           => $data['date_of_birth'] ?? null,
                'gender'                  => $data['gender'] ?? null,
                'phone'                   => $data['phone'] ?? null,
                'address'                 => $data['address'] ?? null,
                'blood_group'             => $data['blood_group'] ?? null,
                'allergies'               => $data['allergies'] ?? null,
                'emergency_contact_name'  => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'medical_notes'           => $data['medical_notes'] ?? null,
            ]);
        });

        return redirect()->route('patients.index')
                         ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.doctor.user', 'appointments.payment']);
        $appointments = $patient->appointments()->with('doctor.user', 'payment')
                                ->latest('appointment_date')->paginate(10);
        return view('patients.show', compact('patient', 'appointments'));
    }

    public function edit(Patient $patient)
    {
        $patient->load('user');
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|unique:users,email,' . $patient->user_id,
            'date_of_birth'           => 'nullable|date|before:today',
            'gender'                  => 'nullable|in:male,female,other',
            'phone'                   => 'nullable|string|max:20',
            'address'                 => 'nullable|string',
            'blood_group'             => 'nullable|string|max:10',
            'allergies'               => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $patient) {
            $patient->user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            $patient->update([
                'date_of_birth'           => $data['date_of_birth'] ?? null,
                'gender'                  => $data['gender'] ?? null,
                'phone'                   => $data['phone'] ?? null,
                'address'                 => $data['address'] ?? null,
                'blood_group'             => $data['blood_group'] ?? null,
                'allergies'               => $data['allergies'] ?? null,
                'emergency_contact_name'  => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'medical_notes'           => $data['medical_notes'] ?? null,
            ]);
        });

        return redirect()->route('patients.show', $patient)
                         ->with('success', 'Patient record updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        // Soft-delete appointments; hard-delete patient + user
        $patient->appointments()->delete();
        $patient->user()->delete();
        $patient->delete();

        return redirect()->route('patients.index')
                         ->with('success', 'Patient record deleted.');
    }

    /** AJAX instant search endpoint */
    public function search(Request $request)
    {
        $term = $request->get('q', '');

        $patients = Patient::with('user')
            ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$term}%"))
            ->orWhere('patient_number', 'like', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->user->name,
                'patient_number' => $p->patient_number,
                'phone'          => $p->phone,
            ]);

        return response()->json($patients);
    }
}
