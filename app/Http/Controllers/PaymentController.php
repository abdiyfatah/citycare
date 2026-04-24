<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('appointment.patient.user', 'appointment.doctor.user')
            ->when($request->search, function ($q, $s) {
                $q->where('receipt_number', 'like', "%{$s}%")
                  ->orWhereHas('appointment.patient.user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->method, fn($q, $m) => $q->where('payment_method', $m))
            ->when($request->date, fn($q, $d) => $q->whereDate('created_at', $d))
            ->latest();

        // Cashier sees all; patient sees own
        $user = auth()->user();
        if ($user->isPatient()) {
            $query->whereHas('appointment', fn($q) => $q->where('patient_id', $user->patient->id));
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        // Pre-select appointment if passed via query string
        $appointment = $request->appointment_id
            ? Appointment::with('patient.user', 'doctor.user')->findOrFail($request->appointment_id)
            : null;

        $pendingAppointments = Appointment::with('patient.user', 'doctor.user')
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'paid'))
            ->latest('appointment_date')
            ->get();

        return view('payments.create', compact('appointment', 'pendingAppointments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile_money,insurance',
            'notes'          => 'nullable|string',
        ]);

        // Prevent duplicate paid payment for same appointment
        $existing = Payment::where('appointment_id', $data['appointment_id'])
                           ->where('status', 'paid')->exists();
        if ($existing) {
            return back()->withErrors(['appointment_id' => 'This appointment already has a completed payment.']);
        }

        Payment::create([
            'appointment_id' => $data['appointment_id'],
            'receipt_number' => Payment::generateReceiptNumber(),
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'status'         => 'paid',
            'paid_at'        => now(),
            'notes'          => $data['notes'] ?? null,
        ]);

        return redirect()->route('payments.index')
                         ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('appointment.patient.user', 'appointment.doctor.user', 'appointment.doctor.department');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load('appointment.patient.user');
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile_money,insurance',
            'status'         => 'required|in:pending,paid,refunded',
            'notes'          => 'nullable|string',
        ]);

        $payment->update([
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'status'         => $data['status'],
            'paid_at'        => $data['status'] === 'paid' ? now() : $payment->paid_at,
            'notes'          => $data['notes'] ?? null,
        ]);

        return redirect()->route('payments.show', $payment)
                         ->with('success', 'Payment record updated.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment record deleted.');
    }
}
