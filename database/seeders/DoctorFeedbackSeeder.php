<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DoctorEvaluation;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;

class DoctorFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Feedback 1: Rahim for Dr. Tariq
        $patient1 = Patient::find(2);
        $doctor1 = Doctor::find(2);
        $appointment1 = Appointment::where('patient_id', 2)->where('doctor_id', 2)->first();

        if ($patient1 && $doctor1) {
            DoctorEvaluation::updateOrCreate(
                ['patient_id' => 2, 'doctor_id' => 2],
                [
                    'appointment_id' => $appointment1 ? $appointment1->id : 1,
                    'rating_1_to_5' => 5,
                    'feedback_text' => "Dr. Tariq is extremely professional. He explained my heart condition in simple terms that I could actually understand. Very grateful for his care.",
                    'consultation_time_minutes' => 25
                ]
            );
        }

        // Feedback 2: Fatema for Dr. Salma
        $patient2 = Patient::find(3);
        $doctor2 = Doctor::find(3);
        $appointment2 = Appointment::where('patient_id', 3)->where('doctor_id', 3)->first();

        if ($patient2 && $doctor2) {
            DoctorEvaluation::updateOrCreate(
                ['patient_id' => 3, 'doctor_id' => 3],
                [
                    'appointment_id' => $appointment2 ? $appointment2->id : 2,
                    'rating_1_to_5' => 4,
                    'feedback_text' => "Dr. Salma was very patient during my neurology consult. She took the time to answer all my questions, though the clinic was quite busy.",
                    'consultation_time_minutes' => 30
                ]
            );
        }

        // Feedback 3: Karim for Dr. Shireen
        $patient3 = Patient::find(4);
        $doctor3 = Doctor::find(7);
        $appointment3 = Appointment::where('patient_id', 4)->where('doctor_id', 7)->first();

        if ($patient3 && $doctor3) {
            DoctorEvaluation::updateOrCreate(
                ['patient_id' => 4, 'doctor_id' => 7],
                [
                    'appointment_id' => $appointment3 ? $appointment3->id : 3,
                    'rating_1_to_5' => 5,
                    'feedback_text' => "Dr. Shireen is one of the best cardiologists I've seen. She was very thorough with my checkup and made me feel at ease throughout the process.",
                    'consultation_time_minutes' => 20
                ]
            );
        }
    }
}
