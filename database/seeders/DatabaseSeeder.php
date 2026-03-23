<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ValidNid;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Generate Valid NIDs
        for ($i = 0; $i < 100; $i++) {
            ValidNid::create([
                'nid_number' => str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT)
            ]);
        }
        $validNids = ValidNid::pluck('nid_number')->toArray();
        $nidIndex = 0;

        // 2. Create 5 Hospitals (Predictable Emails)
        $hospitalData = [
            'Square Hospital' => 'info@square.com',
            'Evercare Hospital' => 'info@evercare.com',
            'United Hospital' => 'info@united.com',
            'Dhaka Medical College' => 'info@dmch.gov.bd',
            'Popular Diagnostic' => 'info@popular.com'
        ];

        $hospitals = [];
        $hIndex = 1;
        foreach ($hospitalData as $name => $email) {
            $user = User::create([
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'hospital',
            ]);
            $hospitals[] = Hospital::create([
                'user_id' => $user->id,
                'dghs_reg_number' => 'DGHS-' . str_pad($hIndex++, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'address' => 'Dhaka, Bangladesh',
                'emergency_contact' => '0171100' . str_pad($hIndex, 4, '0', STR_PAD_LEFT),
            ]);
        }

        // 3. Create 10 Doctors (Predictable Emails: dr.firstname@hospital.com)
        $doctorData = [
            ['name' => 'Dr. Tariq Rahman', 'specialty' => 'Cardiology', 'email' => 'dr.tariq@square.com'],
            ['name' => 'Dr. Salma Ahmed', 'specialty' => 'Neurology', 'email' => 'dr.salma@evercare.com'],
            ['name' => 'Dr. Kamal Hossain', 'specialty' => 'Internal Medicine', 'email' => 'dr.kamal@united.com'],
            ['name' => 'Dr. Farhana Yasmin', 'specialty' => 'Orthopedics', 'email' => 'dr.farhana@dmch.gov.bd'],
            ['name' => 'Dr. Rafiqul Islam', 'specialty' => 'Pediatrics', 'email' => 'dr.rafiqul@popular.com'],
            ['name' => 'Dr. Shireen Haque', 'specialty' => 'Cardiology', 'email' => 'dr.shireen@square.com'],
            ['name' => 'Dr. Anamul Hasan', 'specialty' => 'Neurology', 'email' => 'dr.anamul@evercare.com'],
            ['name' => 'Dr. Nusrat Jahan', 'specialty' => 'Internal Medicine', 'email' => 'dr.nusrat@united.com'],
            ['name' => 'Dr. Mahmudul Hasan', 'specialty' => 'Orthopedics', 'email' => 'dr.mahmudul@dmch.gov.bd'],
            ['name' => 'Dr. Zeba Fariha', 'specialty' => 'Pediatrics', 'email' => 'dr.zeba@popular.com'],
        ];

        $doctors = [];
        foreach ($doctorData as $index => $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'role' => 'doctor',
            ]);
            $nameParts = explode(' ', str_replace('Dr. ', '', $data['name']), 2);
            $doctors[] = Doctor::create([
                'user_id' => $user->id,
                'hospital_id' => $hospitals[$index % 5]->id,
                'bmdc_number' => 'BMDC-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'specialty' => $data['specialty'],
                'qualifications' => 'MBBS, FCPS',
            ]);
        }

        // 4. Create 10 Predictable Patients
        $patientData = [
            ['name' => 'Rahim Uddin', 'email' => 'rahim.uddin@patient.com', 'gender' => 'male', 'blood' => 'O+'],
            ['name' => 'Fatema Begum', 'email' => 'fatema.begum@patient.com', 'gender' => 'female', 'blood' => 'B+'],
            ['name' => 'Karim Ali', 'email' => 'karim.ali@patient.com', 'gender' => 'male', 'blood' => 'A+'],
            ['name' => 'Sumaiya Akter', 'email' => 'sumaiya.akter@patient.com', 'gender' => 'female', 'blood' => 'AB+'],
            ['name' => 'Kazi Noman', 'email' => 'kazi.noman@patient.com', 'gender' => 'male', 'blood' => 'O-'],
            ['name' => 'Tariq Hasan', 'email' => 'tariq.hasan@patient.com', 'gender' => 'male', 'blood' => 'B-'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat.jahan@patient.com', 'gender' => 'female', 'blood' => 'A-'],
            ['name' => 'Arif Hossain', 'email' => 'arif.hossain@patient.com', 'gender' => 'male', 'blood' => 'O+'],
            ['name' => 'Meherun Nesa', 'email' => 'meherun.nesa@patient.com', 'gender' => 'female', 'blood' => 'AB-'],
            ['name' => 'Habibullah', 'email' => 'habibullah@patient.com', 'gender' => 'male', 'blood' => 'B+']
        ];

        $patients = [];
        foreach ($patientData as $index => $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'role' => 'patient',
            ]);
            $nameParts = explode(' ', $data['name'], 2);
            $patients[] = Patient::create([
                'user_id' => $user->id,
                'nid' => $validNids[$nidIndex++],
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'date_of_birth' => Carbon::now()->subYears(30 + $index)->format('Y-m-d'),
                'gender' => $data['gender'],
                'blood_group' => $data['blood'],
                'phone' => '0181100' . str_pad($index, 4, '0', STR_PAD_LEFT),
                'address' => 'Dhaka, Bangladesh',
            ]);
        }

        // 5. Build Rich History For EVERY Patient

        // --- Patient 0: Rahim Uddin ---
        Appointment::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id, // Dr. Tariq
            'hospital_id' => $hospitals[0]->id,
            'date' => Carbon::now()->subDays(60),
            'time_slot' => '10:00 AM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id,
            'record_type' => 'prescription',
            'diagnosis' => 'Hypertension',
            'medications_or_results' => "Amlodipine 5mg (1-0-0)",
            'date' => Carbon::now()->subDays(60)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Routine Checkup',
            'medications_or_results' => "ECG: Normal. Blood Pressure: 140/90.",
            'date' => Carbon::now()->subDays(60)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id,
            'record_type' => 'vaccination',
            'diagnosis' => 'Covid-19 Immunization',
            'medications_or_results' => "Covid-19 Astrazeneca Dose 1",
            'date' => Carbon::now()->subYears(2)
        ]);

        // --- Patient 1: Fatema Begum ---
        Appointment::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doctors[1]->id, // Dr. Salma
            'hospital_id' => $hospitals[1]->id,
            'date' => Carbon::now()->subDays(15),
            'time_slot' => '11:00 AM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doctors[1]->id,
            'record_type' => 'prescription',
            'diagnosis' => 'Migraine',
            'medications_or_results' => "Sumatriptan 50mg (when needed), Naproxen 500mg.",
            'date' => Carbon::now()->subDays(15)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doctors[1]->id,
            'record_type' => 'vaccination',
            'diagnosis' => 'Routine Immunization',
            'medications_or_results' => "Hepatitis B Vaccine - Dose 2",
            'date' => Carbon::now()->subMonths(6)
        ]);

        // --- Patient 2: Karim Ali ---
        Appointment::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id, // Dr. Kamal
            'hospital_id' => $hospitals[2]->id,
            'date' => Carbon::now()->subDays(5),
            'time_slot' => '04:00 PM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Dengue Suspected',
            'medications_or_results' => "CBC & Dengue NS1 - Result: Positive.\nPlatelets: 120,000/mcL. Admit to hospital immediately.",
            'date' => Carbon::now()->subDays(5)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'discharge_summary',
            'diagnosis' => 'Recovered from Dengue',
            'medications_or_results' => "Patient stabilized. Platelets rose to 200,000/mcL. Discharged with advice to rest.",
            'date' => Carbon::now()->subDays(1)
        ]);

        // --- Patient 3: Sumaiya Akter ---
        Appointment::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doctors[3]->id, // Dr. Farhana
            'hospital_id' => $hospitals[3]->id,
            'date' => Carbon::now()->subDays(40),
            'time_slot' => '05:00 PM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doctors[3]->id,
            'record_type' => 'prescription',
            'diagnosis' => 'Vitamin D Deficiency',
            'medications_or_results' => "Cholecalciferol 40000 IU (1 capsule per week for 8 weeks).",
            'date' => Carbon::now()->subDays(40)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doctors[3]->id,
            'record_type' => 'vaccination',
            'diagnosis' => 'Annual Flu',
            'medications_or_results' => "Influenza Vaccine (Vaxigrip Tetra)",
            'date' => Carbon::now()->subDays(100)
        ]);

        // --- Patient 4: Kazi Noman ---
        Appointment::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doctors[4]->id, // Dr. Rafiqul
            'hospital_id' => $hospitals[4]->id,
            'date' => Carbon::now()->subDays(10),
            'time_slot' => '06:00 PM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doctors[4]->id,
            'record_type' => 'prescription',
            'diagnosis' => 'Viral Fever',
            'medications_or_results' => "Paracetamol 500mg(1-1-1), Desloratadine 5mg(0-0-1 for 7 days).",
            'date' => Carbon::now()->subDays(10)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doctors[4]->id,
            'record_type' => 'vaccination',
            'diagnosis' => 'Travel Immunization',
            'medications_or_results' => "Typhoid Conjugate Vaccine",
            'date' => Carbon::now()->subDays(200)
        ]);

        // --- Patient 5: Tariq Hasan ---
        MedicalRecord::create([
            'patient_id' => $patients[5]->id,
            'doctor_id' => $doctors[5]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Annual Blood Work',
            'medications_or_results' => "Hemoglobin: 14.5 g/dL\nWBC: 6500 /mcL\nPlatelets: 250,000 /mcL",
            'date' => Carbon::now()->subDays(12)
        ]);

        // --- Patient 6: Nusrat Jahan ---
        MedicalRecord::create([
            'patient_id' => $patients[6]->id,
            'doctor_id' => $doctors[6]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Thyroid Checkup',
            'medications_or_results' => "TSH: 2.5 mIU/L\nFree T4: 1.2 ng/dL\nNormal Range",
            'date' => Carbon::now()->subDays(5)
        ]);

        // --- Patient 7: Arif Hossain ---
        MedicalRecord::create([
            'patient_id' => $patients[7]->id,
            'doctor_id' => $doctors[3]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Lipid Profile',
            'medications_or_results' => "Cholesterol: 190 mg/dL\nHDL: 50 mg/dL\nLDL: 110 mg/dL",
            'date' => Carbon::now()->subDays(22)
        ]);

        // --- Patient 8: Meherun Nesa ---
        MedicalRecord::create([
            'patient_id' => $patients[8]->id,
            'doctor_id' => $doctors[1]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Diabetes Screening',
            'medications_or_results' => "Fasting Blood Sugar: 95 mg/dL\nHbA1c: 5.4%",
            'date' => Carbon::now()->subDays(30)
        ]);

        // --- Patient 9: Habibullah ---
        MedicalRecord::create([
            'patient_id' => $patients[9]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'lab_report',
            'diagnosis' => 'Liver Function Test',
            'medications_or_results' => "ALT: 25 U/L\nAST: 20 U/L\nBilirubin: 0.8 mg/dL",
            'date' => Carbon::now()->subDays(8)
        ]);

        // --- Add some generic upcoming appointments for UI testing ---
        Appointment::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id,
            'hospital_id' => $hospitals[0]->id,
            'date' => Carbon::now()->addDays(2),
            'time_slot' => '11:00 AM',
            'status' => 'pending'
        ]);
        Appointment::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doctors[1]->id,
            'hospital_id' => $hospitals[1]->id,
            'date' => Carbon::now()->addDays(5),
            'time_slot' => '12:00 PM',
            'status' => 'pending'
        ]);
    }
}
