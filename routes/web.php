<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ── Public ─────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ── Admin ──────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
     });

// ── Receptionist dashboard ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:receptionist,admin'])
     ->prefix('receptionist')
     ->name('receptionist.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'receptionist'])->name('dashboard');
     });

// ── Doctor dashboard ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:doctor'])
     ->prefix('doctor')
     ->name('doctor.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'doctor'])->name('dashboard');
     });

// ── Cashier dashboard ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:cashier,admin'])
     ->prefix('cashier')
     ->name('cashier.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'cashier'])->name('dashboard');
     });

// ── Patient dashboard ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:patient'])
     ->prefix('patient')
     ->name('patient.')
     ->group(function () {
         Route::get('/dashboard', [DashboardController::class, 'patient'])->name('dashboard');
     });

// ── Patients CRUD (admin + receptionist) ──────────────────────────────────────
Route::middleware(['auth', 'role:admin,receptionist'])
     ->group(function () {
         Route::resource('patients', PatientController::class);
     });

// ── Doctors CRUD (admin only) ──────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
     ->group(function () {
         Route::resource('doctors', DoctorController::class);
         Route::resource('departments', DepartmentController::class);
     });

// ── Appointments (multi-role) ─────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,receptionist,doctor,patient'])
     ->group(function () {
         Route::resource('appointments', AppointmentController::class);
         Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
              ->name('appointments.cancel');
     });

// ── Payments (cashier + admin) ────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,cashier,patient'])
     ->group(function () {
         Route::resource('payments', PaymentController::class);
     });

// ── Reports ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,receptionist,cashier'])
     ->prefix('reports')
     ->name('reports.')
     ->group(function () {
         Route::get('/',                [ReportController::class, 'index'])->name('index');
         Route::get('/appointments',    [ReportController::class, 'appointments'])->name('appointments');
         Route::get('/payments',        [ReportController::class, 'payments'])->name('payments');
         Route::get('/doctor-schedule', [ReportController::class, 'doctorSchedule'])->name('doctor-schedule');
         Route::get('/patient-visits',  [ReportController::class, 'patientVisits'])->name('patient-visits');
     });

// ── API / AJAX endpoints (authenticated) ─────────────────────────────────────
Route::middleware('auth')
     ->prefix('api')
     ->name('api.')
     ->group(function () {
         // Dynamic slot loading when booking an appointment
         Route::get('/available-slots',  [DoctorController::class, 'availableSlots'])->name('available-slots');
         // Instant patient search for autocomplete
         Route::get('/patients/search',  [PatientController::class, 'search'])->name('patients.search');
     });
