<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\DoctorReview;

class DoctorReviewSeeder extends Seeder
{
    public function run(): void
    {
        $completedAppointments = Appointment::where('status', 'completed')->get();

        foreach ($completedAppointments as $app) {
            // Check if review already exists to avoid duplicates
            if (DoctorReview::where('appointment_id', $app->id)->exists()) {
                continue;
            }

            // 70% chance to leave a review
            if (rand(1, 10) <= 7) {
                DoctorReview::create([
                    'patient_id' => $app->patient_id,
                    'doctor_id' => $app->doctor_id,
                    'appointment_id' => $app->id,
                    'rating' => rand(3, 5),
                    'comment' => $this->getRandomComment(),
                ]);
            }
        }
    }

    private function getRandomComment()
    {
        $comments = [
            'Great doctor, very attentive!',
            'The wait time was a bit long but the consultation was excellent.',
            'Highly recommend this doctor for any concerns.',
            'Professional and clear explanation of my health status.',
            'Wonderful experience, very friendly staff.',
            'Very helpful and knowledgeable.',
            'Satisfied with the treatment plan.',
            'Excellent care and attention to detail.',
        ];
        return $comments[array_rand($comments)];
    }
}
