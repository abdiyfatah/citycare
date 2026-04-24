<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with('user', 'department')
            ->when($request->search, function ($q, $s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhere('specialisation', 'like', "%{$s}%");
            })
            ->when($request->department_id, fn($q, $d) => $q->where('department_id', $d))
            ->when($request->status !== null, fn($q) => $q->where('is_active', $request->status))
            ->latest();

        $doctors     = $query->paginate(15)->withQueryString();
        $departments = Department::active()->get();

        return view('doctors.index', compact('doctors', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('doctors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'department_id'    => 'required|exists:departments,id',
            'specialisation'   => 'required|string|max:255',
            'qualification'    => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'bio'              => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'schedule'         => 'nullable|array',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'doctor',
            ]);

            Doctor::create([
                'user_id'          => $user->id,
                'department_id'    => $data['department_id'],
                'specialisation'   => $data['specialisation'],
                'qualification'    => $data['qualification'] ?? null,
                'phone'            => $data['phone'] ?? null,
                'bio'              => $data['bio'] ?? null,
                'consultation_fee' => $data['consultation_fee'] ?? 0,
                'schedule'         => $data['schedule'] ?? [],
            ]);
        });

        return redirect()->route('doctors.index')
                         ->with('success', 'Doctor profile created successfully.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('user', 'department');
        $appointments = $doctor->appointments()
            ->with('patient.user', 'payment')
            ->latest('appointment_date')
            ->paginate(10);

        return view('doctors.show', compact('doctor', 'appointments'));
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $departments = Department::active()->get();
        return view('doctors.edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $doctor->user_id,
            'department_id'    => 'required|exists:departments,id',
            'specialisation'   => 'required|string|max:255',
            'qualification'    => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'bio'              => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'is_active'        => 'boolean',
            'schedule'         => 'nullable|array',
        ]);

        DB::transaction(function () use ($data, $doctor) {
            $doctor->user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            $doctor->update([
                'department_id'    => $data['department_id'],
                'specialisation'   => $data['specialisation'],
                'qualification'    => $data['qualification'] ?? null,
                'phone'            => $data['phone'] ?? null,
                'bio'              => $data['bio'] ?? null,
                'consultation_fee' => $data['consultation_fee'] ?? 0,
                'is_active'        => $request->boolean('is_active'),
                'schedule'         => $data['schedule'] ?? [],
            ]);
        });

        return redirect()->route('doctors.show', $doctor)
                         ->with('success', 'Doctor profile updated.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->appointments()->delete();
        $doctor->user()->delete();
        $doctor->delete();

        return redirect()->route('doctors.index')
                         ->with('success', 'Doctor record removed.');
    }

    /** API: return available slots for a doctor on a given date */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $slots  = $doctor->availableSlotsForDate($request->date);

        return response()->json([
            'slots'       => $slots,
            'doctor_name' => $doctor->user->name,
            'fee'         => $doctor->consultation_fee,
        ]);
    }
}
