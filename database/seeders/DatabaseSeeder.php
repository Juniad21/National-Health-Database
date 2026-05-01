<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ValidNid;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\LabTestCatalog;
use App\Models\LabOrder;
use App\Models\Vaccination;
use App\Models\HospitalResource;
use App\Models\DoctorEvaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 0. Create Government Admin
        User::create([
            'email' => 'admin@govt.bd',
            'password' => Hash::make('12345678'),
            'role' => 'govt_admin',
            'nid' => '0000000000',
        ]);

        // 1. Generate 100 Valid NIDs
        for ($i = 0; $i < 100; $i++) {
            ValidNid::create([
                'nid_number' => str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT)
            ]);
        }
        $validNids = ValidNid::pluck('nid_number')->toArray();
        $nidIndex = 0;

        // 1.5 Seed Lab Test Catalog
        $labTests = [
            ['test_name' => 'Complete Blood Count (CBC)', 'description' => 'Measures red & white blood cells, and platelets.'],
            ['test_name' => 'Lipid Panel', 'description' => 'Measures cholesterol and triglyceride levels.'],
            ['test_name' => 'Fasting Blood Sugar (FBS)', 'description' => 'Tests for diabetes and prediabetes.'],
            ['test_name' => 'Liver Function Test (LFT)', 'description' => 'Checks liver health and enzymes.'],
            ['test_name' => 'Kidney Function Test (KFT)', 'description' => 'Measures urea, creatinine, and electrolytes.'],
            ['test_name' => 'Thyroid Profile (T3, T4, TSH)', 'description' => 'Measures thyroid gland function.'],
            ['test_name' => 'Urine Routine', 'description' => 'Macroscopic and microscopic analysis of urine.'],
            ['test_name' => 'Chest X-Ray', 'description' => 'Radiographic image of the chest and lungs.'],
            ['test_name' => 'ECG (Electrocardiogram)', 'description' => 'Records the electrical signal from the heart.'],
            ['test_name' => 'Dengue NS1 Antigen', 'description' => 'Rapid test for early detection of dengue virus.']
        ];
        foreach ($labTests as $test) {
            LabTestCatalog::create($test);
        }

        // 2. Create 10 Hospitals (Predictable Emails)
        $hospitalData = [
            'Square Hospital' => 'info@square.com',
            'Evercare Hospital' => 'info@evercare.com',
            'United Hospital' => 'info@united.com',
            'Dhaka Medical College' => 'info@dmch.gov.bd',
            'Popular Diagnostic' => 'info@popular.com',
            'Apollo Hospital' => 'info@apollo.com',
            'Bangabandhu Sheikh Mujib Medical University' => 'info@bsmmu.edu.bd',
            'Labaid Hospital' => 'info@labaid.com',
            'Ibn Sina Hospital' => 'info@ibnsina.com',
            'Medinova Hospital' => 'info@medinova.com'
        ];

        $hospitals = [];
        $hIndex = 1;
        foreach ($hospitalData as $name => $email) {
            $user = User::create([
                'email' => $email,
                'password' => Hash::make('12345678'),
                'role' => 'hospital',
                'nid' => $validNids[$nidIndex++],
            ]);
            $hospitals[] = Hospital::create([
                'user_id' => $user->id,
                'dghs_reg_number' => 'DGHS-' . str_pad($hIndex++, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'address' => 'Dhaka, Bangladesh',
                'emergency_contact' => '0171100' . str_pad($hIndex, 4, '0', STR_PAD_LEFT),
            ]);
        }

        // 2.5 Seed Hospital Resources
        foreach ($hospitals as $hospital) {
            HospitalResource::create(['hospital_id' => $hospital->id, 'resource_type' => 'General Bed', 'total_capacity' => 100, 'currently_in_use' => random_int(50, 95)]);
            HospitalResource::create(['hospital_id' => $hospital->id, 'resource_type' => 'ICU Unit', 'total_capacity' => 20, 'currently_in_use' => random_int(10, 19)]);
            HospitalResource::create(['hospital_id' => $hospital->id, 'resource_type' => 'Ventilator', 'total_capacity' => 10, 'currently_in_use' => random_int(2, 8)]);
            HospitalResource::create(['hospital_id' => $hospital->id, 'resource_type' => 'Blood Bank', 'total_capacity' => 500, 'currently_in_use' => random_int(100, 400)]);
        }

        // 3. Create 10 Doctors
        // hospital_index maps each doctor to their correct hospital by email domain:
        // 0=Square, 1=Evercare, 2=United, 3=Dhaka Medical, 4=Popular, 5=Apollo
        $doctorData = [
            ['name' => 'Test Doctor',        'specialty' => 'General Practice',  'email' => 'test@doctor.com',           'hospital_index' => 0],
            ['name' => 'Dr. Tariq Rahman',   'specialty' => 'Cardiology',        'email' => 'dr.tariq@square.com',       'hospital_index' => 0],
            ['name' => 'Dr. Salma Ahmed',    'specialty' => 'Neurology',         'email' => 'dr.salma@evercare.com',     'hospital_index' => 1],
            ['name' => 'Dr. Kamal Hossain', 'specialty' => 'Internal Medicine', 'email' => 'dr.kamal@united.com',      'hospital_index' => 2],
            ['name' => 'Dr. Farhana Yasmin','specialty' => 'Orthopedics',       'email' => 'dr.farhana@dmch.gov.bd',   'hospital_index' => 3],
            ['name' => 'Dr. Rafiqul Islam', 'specialty' => 'Pediatrics',        'email' => 'dr.rafiqul@popular.com',   'hospital_index' => 4],
            ['name' => 'Dr. Shireen Haque', 'specialty' => 'Cardiology',        'email' => 'dr.shireen@square.com',    'hospital_index' => 0],
            ['name' => 'Dr. Anamul Hasan',  'specialty' => 'Neurology',         'email' => 'dr.anamul@evercare.com',   'hospital_index' => 1],
            ['name' => 'Dr. Nusrat Jahan',  'specialty' => 'Internal Medicine', 'email' => 'dr.nusrat@united.com',     'hospital_index' => 2],
            ['name' => 'Dr. Mahmudul Hasan','specialty' => 'Orthopedics',       'email' => 'dr.mahmudul@dmch.gov.bd',  'hospital_index' => 3],
        ];

        $doctors = [];
        foreach ($doctorData as $index => $data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'role' => 'doctor',
                'nid' => $validNids[$nidIndex++],
            ]);
            $nameParts = explode(' ', str_replace('Dr. ', '', $data['name']), 2);
            $doctors[] = Doctor::create([
                'user_id' => $user->id,
                'hospital_id' => $hospitals[$data['hospital_index']]->id,
                'bmdc_number' => 'BMDC-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'specialty' => $data['specialty'],
                'qualifications' => 'MBBS, FCPS',
            ]);
        }

        // 4. Create 10 Predictable Patients
        $patientData = [
            ['name' => 'Test Patient', 'email' => 'test@patient.com', 'gender' => 'male', 'blood' => 'O+'],
            ['name' => 'Rahim Uddin', 'email' => 'rahim.uddin@patient.com', 'gender' => 'male', 'blood' => 'O+'],
            ['name' => 'Fatema Begum', 'email' => 'fatema.begum@patient.com', 'gender' => 'female', 'blood' => 'B+'],
            ['name' => 'Karim Ali', 'email' => 'karim.ali@patient.com', 'gender' => 'male', 'blood' => 'A+'],
            ['name' => 'Sumaiya Akter', 'email' => 'sumaiya.akter@patient.com', 'gender' => 'female', 'blood' => 'AB+'],
            ['name' => 'Kazi Noman', 'email' => 'kazi.noman@patient.com', 'gender' => 'male', 'blood' => 'O-'],
            ['name' => 'Tariq Hasan', 'email' => 'tariq.hasan@patient.com', 'gender' => 'male', 'blood' => 'B-'],
            ['name' => 'Nusrat Jahan', 'email' => 'nusrat.jahan@patient.com', 'gender' => 'female', 'blood' => 'A-'],
            ['name' => 'Arif Hossain', 'email' => 'arif.hossain@patient.com', 'gender' => 'male', 'blood' => 'O+'],
            ['name' => 'Habibullah', 'email' => 'habibullah@patient.com', 'gender' => 'male', 'blood' => 'B+']
        ];

        $patients = [];
        foreach ($patientData as $index => $data) {
            $nid = $validNids[$nidIndex++];
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
                'role' => 'patient',
                'nid' => $nid,
            ]);
            $nameParts = explode(' ', $data['name'], 2);
            $patients[] = Patient::create([
                'user_id' => $user->id,
                'nid' => $nid,
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

        // --- Patient 0: Test Patient ---
        Appointment::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id, 
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
            'record_type' => 'lab',
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

        // --- Patient 1: Rahim Uddin ---
        Appointment::create([
            'patient_id' => $patients[1]->id,
            'doctor_id' => $doctors[1]->id, 
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

        // --- Patient 2: Fatema Begum ---
        Appointment::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id, 
            'hospital_id' => $hospitals[2]->id,
            'date' => Carbon::now()->subDays(5),
            'time_slot' => '04:00 PM',
            'status' => 'completed'
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Dengue Suspected',
            'medications_or_results' => "CBC & Dengue NS1 - Result: Positive.\nPlatelets: 120,000/mcL. Admit to hospital immediately.",
            'date' => Carbon::now()->subDays(5)
        ]);
        MedicalRecord::create([
            'patient_id' => $patients[2]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'document',
            'diagnosis' => 'Recovered from Dengue',
            'medications_or_results' => "Patient stabilized. Platelets rose to 200,000/mcL. Discharged with advice to rest.",
            'date' => Carbon::now()->subDays(1)
        ]);

        // --- Patient 3: Karim Ali ---
        Appointment::create([
            'patient_id' => $patients[3]->id,
            'doctor_id' => $doctors[3]->id, 
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

        // --- Patient 4: Sumaiya Akter ---
        Appointment::create([
            'patient_id' => $patients[4]->id,
            'doctor_id' => $doctors[4]->id, 
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

        // --- Patient 5: Kazi Noman ---
        MedicalRecord::create([
            'patient_id' => $patients[5]->id,
            'doctor_id' => $doctors[5]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Annual Blood Work',
            'medications_or_results' => "Hemoglobin: 14.5 g/dL\nWBC: 6500 /mcL\nPlatelets: 250,000 /mcL",
            'date' => Carbon::now()->subDays(12)
        ]);

        // --- Patient 6: Tariq Hasan ---
        MedicalRecord::create([
            'patient_id' => $patients[6]->id,
            'doctor_id' => $doctors[6]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Thyroid Checkup',
            'medications_or_results' => "TSH: 2.5 mIU/L\nFree T4: 1.2 ng/dL\nNormal Range",
            'date' => Carbon::now()->subDays(5)
        ]);

        // --- Patient 7: Nusrat Jahan ---
        MedicalRecord::create([
            'patient_id' => $patients[7]->id,
            'doctor_id' => $doctors[3]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Lipid Profile',
            'medications_or_results' => "Cholesterol: 190 mg/dL\nHDL: 50 mg/dL\nLDL: 110 mg/dL",
            'date' => Carbon::now()->subDays(22)
        ]);

        // --- Patient 8: Arif Hossain ---
        MedicalRecord::create([
            'patient_id' => $patients[8]->id,
            'doctor_id' => $doctors[1]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Diabetes Screening',
            'medications_or_results' => "Fasting Blood Sugar: 95 mg/dL\nHbA1c: 5.4%",
            'date' => Carbon::now()->subDays(30)
        ]);

        // --- Patient 9: Habibullah ---
        MedicalRecord::create([
            'patient_id' => $patients[9]->id,
            'doctor_id' => $doctors[2]->id,
            'record_type' => 'lab',
            'diagnosis' => 'Liver Function Test',
            'medications_or_results' => "ALT: 25 U/L\nAST: 20 U/L\nBilirubin: 0.8 mg/dL",
            'date' => Carbon::now()->subDays(8)
        ]);

        // --- Ensure ALL patients have rich medical history (3 prescriptions, 3 lab records, 2 documents) ---
        foreach ($patients as $index => $patient) {
            $doctor = $doctors[$index % count($doctors)];
            $hospital = $hospitals[$index % count($hospitals)];

            // Create 3 Appointments, Prescriptions, and Lab Reports
            for ($i = 1; $i <= 3; $i++) {
                $randomDate = Carbon::now()->subDays(random_int(1, 120));

                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'hospital_id' => $hospital->id,
                    'date' => $randomDate,
                    'time_slot' => '10:00 AM',
                    'status' => 'completed'
                ]);

                MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'record_type' => 'prescription',
                    'diagnosis' => 'Follow-up Consultation #' . $i,
                    'medications_or_results' => "Medication Course {$i}:\nParacetamol 500mg 1-1-1\nAntacid 20mg 1-0-1",
                    'date' => $randomDate
                ]);

                $labTest = LabTestCatalog::inRandomOrder()->first();
                LabOrder::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'hospital_id' => $hospital->id,
                    'lab_test_catalog_id' => $labTest->id,
                    'status' => 'completed',
                    'result_summary' => 'Tested parameters are within normal reference ranges.'
                ]);

                MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'record_type' => 'lab',
                    'diagnosis' => $labTest->test_name . ' Result',
                    'medications_or_results' => "All parameters detected within standard range.\nNo immediate concerns.",
                    'date' => $randomDate->copy()->addDays(1)
                ]);
            }

            // Create 2 Document Records
            MedicalRecord::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'record_type' => 'document',
                'diagnosis' => 'Discharge Summary',
                'medications_or_results' => "Patient admitted for observation and discharged in stable condition.\nHospital stay duration: 2 days. Instructed to maintain bed rest.",
                'document_path' => 'dummy/discharge_summary.pdf',
                'date' => Carbon::now()->subDays(random_int(30, 60))
            ]);

            MedicalRecord::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'record_type' => 'document',
                'diagnosis' => 'MRI Scan Details',
                'medications_or_results' => "MRI of the lower back.\nNo disc herniations visible. Normal alignment.",
                'document_path' => 'dummy/mri_scan_results.pdf',
                'date' => Carbon::now()->subDays(random_int(60, 100))
            ]);

            // Ensure every patient has health metric data
            \App\Models\PatientHealthMetric::create([
                'patient_id' => $patient->id,
                'user_id' => $patient->user_id,
                'weight_kg' => 70.5 + ($index % 5),
                'systolic_bp' => 120 + ($index % 10),
                'diastolic_bp' => 80 + ($index % 5),
                'heart_rate' => 72 + ($index % 8),
                'bmi' => 22.5 + ($index % 3),
                'recorded_at' => Carbon::now()->subDays(random_int(1, 10))
            ]);

            // Baseline Vaccination Schedule for every patient
            $baselineVaccines = [
                ['name' => 'Covid-19 Pfizer (Dose 1)', 'status' => 'taken', 'date' => Carbon::now()->subYears(2)],
                ['name' => 'Covid-19 Pfizer (Dose 2)', 'status' => 'taken', 'date' => Carbon::now()->subYears(1)],
                ['name' => 'Influenza 2026', 'status' => 'pending', 'date' => Carbon::now()->addMonths(2)],
                ['name' => 'Hepatitis B (Dose 1)', 'status' => 'pending', 'date' => Carbon::now()->addDays(15)]
            ];

            foreach ($baselineVaccines as $bv) {
                Vaccination::create([
                    'patient_id' => $patient->id,
                    'vaccine_name' => $bv['name'],
                    'due_date' => $bv['date'],
                    'status' => $bv['status']
                ]);
            }
        }

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

        // --- Add today's queue appointments for EVERY doctor ---
        foreach ($doctors as $doctor) {
            for ($i = 0; $i < 3; $i++) {
                Appointment::create([
                    'patient_id' => $patients[array_rand($patients)]->id,
                    'doctor_id' => $doctor->id,
                    'hospital_id' => $doctor->hospital_id,
                    'date' => Carbon::today(),
                    'time_slot' => '10:' . str_pad($i * 15, 2, '0', STR_PAD_LEFT) . ' AM',
                    'status' => 'pending',
                    'booking_id' => 'BK-' . strtoupper(uniqid()),
                    'token_number' => $i + 1
                ]);
            }
        }

        // --- Add Doctor Evaluations ---
        DoctorEvaluation::create([
            'appointment_id' => Appointment::where('status', 'completed')->first()->id ?? 1,
            'doctor_id' => $doctors[0]->id,
            'patient_id' => $patients[0]->id,
            'rating_1_to_5' => 5,
            'feedback_text' => 'Excellent doctor, very attentive.',
            'consultation_time_minutes' => 15
        ]);

        DoctorEvaluation::create([
            'appointment_id' => Appointment::where('status', 'completed')->skip(1)->first()->id ?? 2,
            'doctor_id' => $doctors[1]->id,
            'patient_id' => $patients[1]->id,
            'rating_1_to_5' => 4,
            'feedback_text' => 'Good consultation but wait time was a bit long.',
            'consultation_time_minutes' => 20
        ]);

        // --- Add explicit vaccinations for test patient ---
        Vaccination::create([
            'patient_id' => $patients[0]->id,
            'vaccine_name' => 'Typhoid Vaccine',
            'due_date' => Carbon::now()->subDays(5),
            'status' => 'pending'
        ]);
        Vaccination::create([
            'patient_id' => $patients[0]->id,
            'vaccine_name' => 'Hepatitis B - Dose 3',
            'due_date' => Carbon::now()->addDays(30),
            'status' => 'pending'
        ]);

        // --- Add Dummy Lab Order ---
        LabOrder::create([
            'patient_id' => $patients[0]->id,
            'doctor_id' => $doctors[0]->id,
            'hospital_id' => $hospitals[0]->id,
            'lab_test_catalog_id' => 1,
            'status' => 'pending'
        ]);

        // --- Create Ambulances for each hospital ---
        foreach ($hospitals as $hospital) {
            // Create 2 ambulances per hospital
            for ($i = 1; $i <= 2; $i++) {
                $ambulanceCode = "AMB-" . $hospital->id . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                \App\Models\Ambulance::create([
                    'hospital_id' => $hospital->id,
                    'ambulance_code' => $ambulanceCode,
                    'vehicle_number' => 'DHK-METRO-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'ambulance_type' => ['Basic Life Support', 'Advanced Life Support', 'ICU Ambulance'][random_int(0, 2)],
                    'capacity' => random_int(1, 2),
                    'current_status' => 'Available',
                    'is_active' => true,
                ]);
            }

            // Seed Blood Stock
            foreach (\App\Models\BloodStock::getBloodGroups() as $group) {
                \App\Models\BloodStock::create([
                    'hospital_id' => $hospital->id,
                    'hospital_name' => $hospital->name,
                    'district' => $hospital->address,
                    'blood_group' => $group,
                    'available_units' => random_int(5, 50),
                    'minimum_required_units' => 10,
                    'last_updated_by' => 1, // Admin
                ]);
            }
        }

        // --- Specific Test Ambulance ---
        \App\Models\Ambulance::create([
            'hospital_id' => $hospitals[0]->id,
            'ambulance_code' => 'TEST-AMB',
            'vehicle_number' => 'DHK-007-AMB',
            'ambulance_type' => 'Advanced Life Support',
            'capacity' => 2,
            'current_status' => 'Available',
            'is_active' => true,
        ]);
    }
}