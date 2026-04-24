<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $stats = [
            'total_patients'      => Patient::count(),
            'total_doctors'       => Doctor::count(),
            'today_appointments'  => Appointment::today()->count(),
            'revenue_this_month'  => Payment::where('status', 'paid')
                                            ->whereMonth('paid_at', now()->month)
                                            ->sum('amount'),
        ];

        $todayAppointments = Appointment::with('patient.user', 'doctor.user')
            ->today()->latest('appointment_slot')->take(8)->get();

        $recentPayments = Payment::with('appointment.patient.user')
            ->where('status', 'paid')->latest()->take(5)->get();

        // Appointments per day for the last 7 days
        $weeklyData = Appointment::select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('appointment_date', [now()->subDays(6), now()])
            ->groupBy('date')
            ->pluck('count', 'date');

        return view('admin.dashboard', compact('stats', 'todayAppointments', 'recentPayments', 'weeklyData'));
    }

    public function receptionist()
    {
        $todayAppointments = Appointment::with('patient.user', 'doctor.user')
            ->today()->orderBy('appointment_slot')->get();

        $upcomingAppointments = Appointment::with('patient.user', 'doctor.user')
            ->upcoming()->take(10)->get();

        $doctors = Doctor::with('user')->active()->get();

        return view('receptionist.dashboard', compact('todayAppointments', 'upcomingAppointments', 'doctors'));
    }

    public function doctor()
    {
        $user   = auth()->user();
        $doctor = $user->doctor;

        $todayAppointments = Appointment::with('patient.user')
            ->today()->forDoctor($doctor->id)
            ->orderBy('appointment_slot')->get();

        $upcomingAppointments = Appointment::with('patient.user')
            ->upcoming()->forDoctor($doctor->id)->take(5)->get();

        $stats = [
            'today_count'     => $todayAppointments->count(),
            'total_patients'  => Appointment::forDoctor($doctor->id)->distinct('patient_id')->count(),
            'completed_today' => $todayAppointments->where('status', 'completed')->count(),
            'pending_today'   => $todayAppointments->whereIn('status', ['pending', 'confirmed'])->count(),
        ];

        return view('doctor.dashboard', compact('doctor', 'todayAppointments', 'upcomingAppointments', 'stats'));
    }

    public function cashier()
    {
        $pendingPayments = Appointment::with('patient.user', 'doctor.user', 'payment')
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'paid'))
            ->latest('appointment_date')->take(15)->get();

        $todayRevenue = Payment::where('status', 'paid')
            ->whereDate('paid_at', today())->sum('amount');

        $recentPayments = Payment::with('appointment.patient.user')
            ->where('status', 'paid')->latest()->take(10)->get();

        return view('cashier.dashboard', compact('pendingPayments', 'todayRevenue', 'recentPayments'));
    }

    public function patient()
    {
        $user    = auth()->user();
        $patient = $user->patient;

        $upcomingAppointments = Appointment::with('doctor.user', 'doctor.department')
            ->where('patient_id', $patient->id)
            ->upcoming()->orderBy('appointment_date')->take(5)->get();

        $recentVisits = Appointment::with('doctor.user', 'payment')
            ->where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->latest('appointment_date')->take(5)->get();

        $payments = Payment::whereHas('appointment', fn($q) => $q->where('patient_id', $patient->id))
            ->latest()->take(5)->get();

        return view('patient.dashboard', compact('patient', 'upcomingAppointments', 'recentVisits', 'payments'));
    }
}
