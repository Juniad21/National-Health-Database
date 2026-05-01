<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DoctorProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $doctor = $user->doctor;
        $profile = $user->doctorProfile ?? new DoctorProfile();
        $hospitals = Hospital::all();

        return view('doctor.profile.edit', compact('user', 'doctor', 'profile', 'hospitals'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'license_number' => 'required|string|max:50',
            'license_expiry_date' => 'nullable|date',
            'specialization' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'medical_college' => 'nullable|string|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'hospital_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'consultation_fee' => 'nullable|numeric|min:0',
            'consultation_type' => 'nullable|string|in:Online,In-person,Both',
            'available_days' => 'nullable|array',
            'available_time_slots' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'services_offered' => 'nullable|string',
            'awards_certifications' => 'nullable|string',
            'emergency_availability' => 'nullable|boolean',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $doctor = $user->doctor;

        // Handle Available Days array to string
        if (isset($validated['available_days'])) {
            $validated['available_days'] = implode(', ', $validated['available_days']);
        }

        // Handle Profile Photo
        if ($request->hasFile('profile_photo')) {
            if ($user->doctorProfile && $user->doctorProfile->profile_photo) {
                Storage::disk('public')->delete($user->doctorProfile->profile_photo);
            }
            $path = $request->file('profile_photo')->store('doctor_photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $validated['user_id'] = $user->id;
        $validated['doctor_id'] = $doctor?->id;
        $validated['emergency_availability'] = $request->has('emergency_availability');

        // Only set Pending status if it's a new profile or something critical changed
        if (!$user->doctorProfile) {
            $validated['verification_status'] = 'Pending';
        }

        DoctorProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('doctor.profile.show')->with('success', 'Professional profile updated successfully!');
    }

    public function show()
    {
        $user = Auth::user();
        $profile = $user->doctorProfile;

        if (!$profile) {
            return redirect()->route('doctor.profile.edit')->with('info', 'Please complete your professional profile first.');
        }

        return view('doctor.profile.show', compact('user', 'profile'));
    }

    public function publicShow($id)
    {
        $doctor = \App\Models\Doctor::with(['hospital', 'user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);
            
        $profile = $doctor->profile;
        $reviews = $doctor->reviews()->with('patient')->latest()->get();

        return view('doctor.profile.public', compact('doctor', 'profile', 'reviews'));
    }
}
