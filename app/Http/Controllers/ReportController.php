<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ── Appointment report ─────────────────────────────────────────────────────

    public function appointments(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'status'    => 'nullable|string',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $query = Appointment::with('patient.user', 'doctor.user', 'payment')
            ->when($request->date_from, fn($q, $d) => $q->whereDate('appointment_date', '>=', $d))
            ->when($request->date_to,   fn($q, $d) => $q->whereDate('appointment_date', '<=', $d))
            ->when($request->status,    fn($q, $s) => $q->where('status', $s))
            ->when($request->doctor_id, fn($q, $d) => $q->where('doctor_id', $d))
            ->orderBy('appointment_date')
            ->orderBy('appointment_slot');

        $appointments = $query->get();
        $doctors      = Doctor::with('user')->active()->get();

        if ($request->format === 'csv') {
            return $this->exportCsv(
                $appointments,
                ['Date', 'Slot', 'Patient', 'Doctor', 'Status', 'Reason'],
                fn($a) => [
                    $a->appointment_date->format('Y-m-d'),
                    $a->appointment_slot,
                    $a->patient->user->name ?? '',
                    $a->doctor->user->name  ?? '',
                    $a->status,
                    $a->reason,
                ],
                'appointments-report'
            );
        }

        return view('reports.appointments', compact('appointments', 'doctors'));
    }

    // ── Payment / revenue report ───────────────────────────────────────────────

    public function payments(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Payment::with('appointment.patient.user', 'appointment.doctor.user')
            ->where('status', 'paid')
            ->when($request->date_from, fn($q, $d) => $q->whereDate('paid_at', '>=', $d))
            ->when($request->date_to,   fn($q, $d) => $q->whereDate('paid_at', '<=', $d))
            ->latest('paid_at');

        $payments     = $query->get();
        $totalRevenue = $payments->sum('amount');

        if ($request->format === 'csv') {
            return $this->exportCsv(
                $payments,
                ['Receipt', 'Date', 'Patient', 'Doctor', 'Method', 'Amount'],
                fn($p) => [
                    $p->receipt_number,
                    $p->paid_at?->format('Y-m-d'),
                    $p->appointment->patient->user->name ?? '',
                    $p->appointment->doctor->user->name  ?? '',
                    $p->payment_method,
                    number_format($p->amount, 2),
                ],
                'payments-report'
            );
        }

        return view('reports.payments', compact('payments', 'totalRevenue'));
    }

    // ── Doctor schedule report ─────────────────────────────────────────────────

    public function doctorSchedule(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date',
        ]);

        $doctor = Doctor::with('user', 'department')->findOrFail($request->doctor_id);

        $appointments = Appointment::with('patient.user', 'payment')
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $request->date)
            ->orderBy('appointment_slot')
            ->get();

        $doctors = Doctor::with('user')->active()->get();

        if ($request->format === 'csv') {
            return $this->exportCsv(
                $appointments,
                ['Slot', 'Patient', 'Status', 'Reason'],
                fn($a) => [
                    $a->appointment_slot,
                    $a->patient->user->name ?? '',
                    $a->status,
                    $a->reason,
                ],
                'doctor-schedule'
            );
        }

        return view('reports.doctor-schedule', compact('doctor', 'appointments', 'doctors'));
    }

    // ── Patient visit history ──────────────────────────────────────────────────

    public function patientVisits(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $patient = Patient::with('user')->findOrFail($request->patient_id);

        $visits = Appointment::with('doctor.user', 'payment')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->get();

        $patients = Patient::with('user')->get();

        if ($request->format === 'csv') {
            return $this->exportCsv(
                $visits,
                ['Date', 'Doctor', 'Status', 'Diagnosis'],
                fn($v) => [
                    $v->appointment_date->format('Y-m-d'),
                    $v->doctor->user->name ?? '',
                    $v->status,
                    $v->diagnosis,
                ],
                'patient-visits'
            );
        }

        return view('reports.patient-visits', compact('patient', 'visits', 'patients'));
    }

    // ── Helper: generic CSV export ─────────────────────────────────────────────

    private function exportCsv($records, array $headers, callable $rowMapper, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($records, $headers, $rowMapper) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($records as $record) {
                fputcsv($handle, $rowMapper($record));
            }
            fclose($handle);
        }, $filename . '-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
