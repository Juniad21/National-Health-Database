@extends('layouts.doctor')

@section('header_title', 'Edit Professional Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- PERSONAL & PROFESSIONAL CARD -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-blue-900 px-8 py-6">
                <h3 class="text-xl font-bold text-white">Professional Information</h3>
                <p class="text-blue-200 text-sm">This information will be visible to patients and admins.</p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Profile Photo -->
                <div class="flex items-center gap-6 pb-6 border-b border-gray-50">
                    <div class="w-24 h-24 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                        @if($profile->profile_photo)
                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Profile Photo</label>
                        <input type="file" name="profile_photo" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[10px] text-gray-400 mt-2">JPG, PNG or GIF. Max 2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Full Name *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? ($user->doctor->first_name . ' ' . $user->doctor->last_name)) }}" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $profile->email ?? $user->email) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Gender</label>
                        <select name="gender" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $profile->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $profile->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $profile->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Specialization *</label>
                        <input type="text" name="specialization" value="{{ old('specialization', $profile->specialization ?? $user->doctor->specialty) }}" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Cardiology, Pediatrics">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">BMDC/License Number *</label>
                        <input type="text" name="license_number" value="{{ old('license_number', $profile->license_number ?? $user->doctor->bmdc_number) }}" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">License Expiry Date</label>
                        <input type="date" name="license_expiry_date" value="{{ old('license_expiry_date', $profile->license_expiry_date?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Years of Experience *</label>
                        <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $profile->years_of_experience) }}" required min="0" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Medical College</label>
                        <input type="text" name="medical_college" value="{{ old('medical_college', $profile->medical_college) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Qualifications / Degrees</label>
                    <textarea name="qualifications" rows="2" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. MBBS, FCPS, MD">{{ old('qualifications', $profile->qualifications ?? $user->doctor->qualifications) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Biography / About</label>
                    <textarea name="biography" rows="4" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="Briefly describe your medical journey and expertise...">{{ old('biography', $profile->biography) }}</textarea>
                </div>
            </div>
        </div>

        <!-- WORK & CONSULTATION CARD -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-800 px-8 py-6">
                <h3 class="text-xl font-bold text-white">Work & Consultation</h3>
                <p class="text-gray-400 text-sm">Details about your current workplace and consultation settings.</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Current Hospital (System List)</label>
                        <select name="hospital_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Hospital</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->id }}" {{ old('hospital_id', $profile->hospital_id ?? $user->doctor->hospital_id) == $hospital->id ? 'selected' : '' }}>{{ $hospital->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Other Hospital/Clinic Name</label>
                        <input type="text" name="hospital_name" value="{{ old('hospital_name', $profile->hospital_name) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="If not in the list above">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Department</label>
                        <input type="text" name="department" value="{{ old('department', $profile->department) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation', $profile->designation) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Senior Consultant">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Consultation Fee (৳)</label>
                        <input type="number" name="consultation_fee" value="{{ old('consultation_fee', $profile->consultation_fee) }}" min="0" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Consultation Type</label>
                        <select name="consultation_type" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="In-person" {{ old('consultation_type', $profile->consultation_type) == 'In-person' ? 'selected' : '' }}>In-person</option>
                            <option value="Online" {{ old('consultation_type', $profile->consultation_type) == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Both" {{ old('consultation_type', $profile->consultation_type) == 'Both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Available Days</label>
                    <div class="flex flex-wrap gap-3">
                        @php $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; @endphp
                        @foreach($days as $day)
                            <label class="flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                                <input type="checkbox" name="available_days[]" value="{{ $day }}" class="rounded text-blue-600 focus:ring-blue-500" {{ in_array($day, explode(', ', $profile->available_days ?? '')) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-gray-700">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Available Time Slots</label>
                    <input type="text" name="available_time_slots" value="{{ old('available_time_slots', $profile->available_time_slots) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 10:00 AM - 02:00 PM, 06:00 PM - 09:00 PM">
                </div>

                <div class="flex items-center gap-3 p-4 bg-red-50 rounded-2xl border border-red-100 mt-4">
                    <input type="checkbox" name="emergency_availability" value="1" {{ old('emergency_availability', $profile->emergency_availability) ? 'checked' : '' }} class="w-5 h-5 rounded text-red-600 focus:ring-red-500">
                    <div>
                        <p class="text-sm font-bold text-red-800">Available for Emergency Calls</p>
                        <p class="text-[11px] text-red-600">Checking this makes you visible in the Emergency SOS directory.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDITIONAL INFO CARD -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-indigo-900 px-8 py-6">
                <h3 class="text-xl font-bold text-white">Additional Details</h3>
                <p class="text-indigo-200 text-sm">Services, awards, and languages.</p>
            </div>

            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Languages Spoken</label>
                    <input type="text" name="languages_spoken" value="{{ old('languages_spoken', $profile->languages_spoken) }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Bengali, English">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Services Offered</label>
                    <textarea name="services_offered" rows="3" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Health Checkup, Vaccinations, Surgery">{{ old('services_offered', $profile->services_offered) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Awards & Certifications</label>
                    <textarea name="awards_certifications" rows="3" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Best Physician 2023, Fellow of American College of Surgeons">{{ old('awards_certifications', $profile->awards_certifications) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pb-12">
            <a href="{{ route('doctor.profile.show') }}" class="px-8 py-4 bg-white border border-gray-200 text-gray-600 font-bold rounded-2xl hover:bg-gray-50 transition-all">Cancel</a>
            <button type="submit" class="px-12 py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all transform hover:-translate-y-1">Save Professional Profile</button>
        </div>
    </form>
</div>
@endsection
