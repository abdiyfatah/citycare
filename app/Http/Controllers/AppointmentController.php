<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('patient.user', 'doctor.user', 'payment')
            ->when($request->search, function ($q, $s) {
                $q->whereHas('patient.user', fn($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('doctor.user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date, fn($q, $d) => $q->whereDate('appointment_date', $d))
            ->when($request->doctor_id, fn($q, $d) => $q->where('doctor_id', $d))
            ->latest('appointment_date');

        // Role-based filtering
        $user = auth()->user();
        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
        }

        $appointments = $query->paginate(15)->withQueryString();
        $doctors      = Doctor::with('user')->active()->get();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create()
    {
        $patients = Patient::with('user')->get();
        $doctors  = Doctor::with('user', 'department')->active()->get();
        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_slot' => 'required|string',
            'reason'           => 'nullable|string|max:500',
        ]);

        // Double-booking prevention
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_slot', $data['appointment_slot'])
            ->whereNotIn('status', ['cancelled'])
            ->withTrashed(false)
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'appointment_slot' => 'This time slot is already booked for the selected doctor. Please choose a different slot.',
            ]);
        }

        Appointment::create($data);

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load('patient.user', 'doctor.user', 'doctor.department', 'payment');
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        if (!$appointment->isEditable()) {
            return back()->with('error', 'This appointment cannot be edited.');
        }

        $patients = Patient::with('user')->get();
        $doctors  = Doctor::with('user', 'department')->active()->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'appointment_date'   => 'required|date',
            'appointment_slot'   => 'required|string',
            'status'             => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'reason'             => 'nullable|string|max:500',
            'consultation_notes' => 'nullable|string',
            'diagnosis'          => 'nullable|string',
            'prescription'       => 'nullable|string',
        ]);

        // Double-booking check (excluding current appointment)
        if ($data['appointment_date'] !== $appointment->appointment_date->format('Y-m-d')
            || $data['appointment_slot'] !== $appointment->appointment_slot) {

            $conflict = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('appointment_date', $data['appointment_date'])
                ->where('appointment_slot', $data['appointment_slot'])
                ->where('id', '!=', $appointment->id)
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'appointment_slot' => 'That time slot is already taken. Please choose another.',
                ]);
            }
        }

        $appointment->update($data);

        return redirect()->route('appointments.show', $appointment)
                         ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete(); // soft delete
        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment cancelled.');
    }

    /** AJAX: cancel via quick action */
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Appointment cancelled.']);
    }
}
