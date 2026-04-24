<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Departments ────────────────────────────────────────────────────────
        $deptNames = [
            ['General Medicine',        'Primary care and general health consultations.'],
            ['Paediatrics',             'Medical care for infants, children, and adolescents.'],
            ['Obstetrics & Gynaecology','Women\'s reproductive health and maternity care.'],
            ['Cardiology',              'Heart and cardiovascular system specialist care.'],
            ['Orthopaedics',            'Bones, joints, and musculoskeletal conditions.'],
            ['Dentistry',               'Oral health, teeth, and gum care.'],
        ];
        foreach ($deptNames as [$name, $desc]) {
            Department::create(['name' => $name, 'description' => $desc]);
        }

        // ── Staff users ────────────────────────────────────────────────────────
        User::create(['name' => 'Dr. Sarah Nakato', 'email' => 'admin@citycare.com',     'password' => Hash::make('password'), 'role' => 'admin']);
        User::create(['name' => 'Grace Auma',       'email' => 'reception@citycare.com', 'password' => Hash::make('password'), 'role' => 'receptionist']);
        User::create(['name' => 'Peter Okello',     'email' => 'cashier@citycare.com',   'password' => Hash::make('password'), 'role' => 'cashier']);

        // ── Doctors ────────────────────────────────────────────────────────────
        $doctorData = [
            ['Dr. James Mugisha',  'doctor@citycare.com',      1, 'General Practitioner', 'MBChB, MMed',            50000],
            ['Dr. Agnes Nambi',    'dr.nambi@citycare.com',    2, 'Paediatrician',        'MBChB, DCH',             60000],
            ['Dr. Kevin Ssemakula','dr.ssemakula@citycare.com',4, 'Cardiologist',         'MBChB, MMed (Internal)', 80000],
            ['Dr. Florence Atim', 'dr.atim@citycare.com',     3, 'Gynaecologist',        'MBChB, MMed (O&G)',      70000],
        ];

        // Build Mon-Fri schedule with 30-min slots 08:00-12:00 and 14:00-17:00
        $slots = [];
        foreach ([8,9,10,11] as $h) { $slots[] = sprintf('%02d:00',$h); $slots[] = sprintf('%02d:30',$h); }
        foreach ([14,15,16] as $h)  { $slots[] = sprintf('%02d:00',$h); $slots[] = sprintf('%02d:30',$h); }
        $schedule = [];
        foreach (['monday','tuesday','wednesday','thursday','friday'] as $day) {
            $schedule[$day] = $slots;
        }

        foreach ($doctorData as [$name,$email,$deptId,$spec,$qual,$fee]) {
            $u = User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make('password'),'role'=>'doctor']);
            Doctor::create(['user_id'=>$u->id,'department_id'=>$deptId,'specialisation'=>$spec,'qualification'=>$qual,'consultation_fee'=>$fee,'schedule'=>$schedule]);
        }

        // ── Patients ────────────────────────────────────────────────────────────
        $patientsData = [
            ['Alice Nakawunde',   'patient@citycare.com',   '1990-05-14','female','0701234567','O+'],
            ['Robert Wasswa',     'rwasswa@example.com',    '1985-11-22','male',  '0782345678','A+'],
            ['Fatuma Nalwanga',   'fnalwanga@example.com',  '1998-03-08','female','0753456789','B+'],
            ['Samuel Kiggundu',   'skiggundu@example.com',  '1975-07-30','male',  '0774567890','AB+'],
            ['Mary Adeke',        'madeke@example.com',     '2001-12-01','female','0765678901','A-'],
            ['John Tumwine',      'jtumwine@example.com',   '1968-09-15','male',  '0756789012','O-'],
            ['Esther Namutebi',   'enamutebi@example.com',  '1993-04-25','female','0747890123','B-'],
            ['David Byaruhanga',  'dbyaruhanga@example.com','1980-01-10','male',  '0738901234','O+'],
        ];
        $year = date('Y');
        foreach ($patientsData as $i => [$name,$email,$dob,$gender,$phone,$blood]) {
            $u = User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make('password'),'role'=>'patient']);
            Patient::create(['user_id'=>$u->id,'patient_number'=>'CC-'.$year.'-'.str_pad($i+1,4,'0',STR_PAD_LEFT),'date_of_birth'=>$dob,'gender'=>$gender,'phone'=>$phone,'blood_group'=>$blood]);
        }

        // ── Appointments ────────────────────────────────────────────────────────
        $patients = Patient::all();
        $doctors  = Doctor::all();
        $seeds = [
            [0,0,now()->format('Y-m-d'),           '08:00','confirmed','Routine check-up',      null,null,null],
            [1,0,now()->format('Y-m-d'),           '08:30','confirmed','Persistent headache',   null,null,null],
            [2,1,now()->format('Y-m-d'),           '09:00','pending',  'Child vaccination',     null,null,null],
            [3,2,now()->format('Y-m-d'),           '09:30','confirmed','Chest pain evaluation', null,null,null],
            [4,3,now()->format('Y-m-d'),           '10:00','pending',  'Prenatal visit',        null,null,null],
            [5,0,now()->subDay()->format('Y-m-d'), '08:00','completed','Flu symptoms',          'Fever and body aches. Prescribed paracetamol.','Influenza','Paracetamol 500mg TDS x 5 days'],
            [6,1,now()->subDay()->format('Y-m-d'), '09:00','completed','Child ear infection',   'Right ear otitis media.','Otitis Media','Amoxicillin 250mg TDS x 7 days'],
            [7,0,now()->subDay()->format('Y-m-d'), '10:00','no_show',  'Back pain',             null,null,null],
            [0,2,now()->addDay()->format('Y-m-d'), '08:00','confirmed','Cardiology follow-up',  null,null,null],
            [1,3,now()->addDays(2)->format('Y-m-d'),'09:00','pending', 'Women\'s health check', null,null,null],
            [2,0,now()->addDays(3)->format('Y-m-d'),'14:00','pending', 'Stomach pain',          null,null,null],
        ];

        foreach ($seeds as [$pi,$di,$date,$slot,$status,$reason,$notes,$diag,$presc]) {
            Appointment::create([
                'patient_id'         => $patients[$pi]->id,
                'doctor_id'          => $doctors[$di]->id,
                'appointment_date'   => $date,
                'appointment_slot'   => $slot,
                'status'             => $status,
                'reason'             => $reason,
                'consultation_notes' => $notes,
                'diagnosis'          => $diag,
                'prescription'       => $presc,
            ]);
        }

        // ── Payments for completed appointments ────────────────────────────────
        $n = 1;
        foreach (Appointment::where('status','completed')->get() as $apt) {
            Payment::create([
                'appointment_id' => $apt->id,
                'receipt_number' => 'RCP-'.$year.'-'.str_pad($n++,4,'0',STR_PAD_LEFT),
                'amount'         => $apt->doctor->consultation_fee,
                'payment_method' => 'cash',
                'status'         => 'paid',
                'paid_at'        => now()->subDay(),
            ]);
        }

        $this->command->info('CityCare seeder complete!');
        $this->command->table(
            ['Role','Email','Password'],
            [
                ['Admin',        'admin@citycare.com',     'password'],
                ['Receptionist', 'reception@citycare.com', 'password'],
                ['Cashier',      'cashier@citycare.com',   'password'],
                ['Doctor',       'doctor@citycare.com',    'password'],
                ['Patient',      'patient@citycare.com',   'password'],
            ]
        );
    }
}
