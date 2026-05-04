@extends('layouts.patient')

@section('header_title', 'Profile Management')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Personal Profile</h2>
            <p class="text-gray-500 text-sm">Keep your health profile up to date for better care.</p>
        </div>

        <form action="{{ route('patient.profile.update') }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">First Name <span class="text-red-400">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 @error('first_name') border-red-500 @enderror">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Gender</label>
                        <select name="gender" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Contact Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" placeholder="+880 1XXX XXXXXX" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                        <textarea name="address" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('address', $patient->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Physical --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Physical Details</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Blood Group</label>
                        <select name="blood_group" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Height (cm)</label>
                        <input type="number" step="0.1" name="height_cm" value="{{ old('height_cm', $patient->height_cm) }}" placeholder="170" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                        @error('height_cm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg', $patient->weight_kg) }}" placeholder="65" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                        @error('weight_kg') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @if($patient->bmi)
                    <div class="p-4 bg-teal-50 rounded-2xl border border-teal-100">
                        <p class="text-xs text-teal-700 font-medium">
                            <strong>Your BMI:</strong> {{ $patient->bmi }} — 
                            <span class="font-black">{{ $patient->bmi_category }}</span>
                        </p>
                    </div>
                @endif
            </div>

            {{-- Emergency Contact --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
            </div>

            {{-- Medical History --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Medical History</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Allergies</label>
                        <textarea name="allergies" rows="2" placeholder="e.g. Penicillin, Pollen, Peanuts" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('allergies', $patient->allergies) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Existing Medical Conditions</label>
                        <textarea name="medical_conditions" rows="2" placeholder="e.g. Diabetes Type 2, Hypertension" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('medical_conditions', $patient->medical_conditions) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Current Medications</label>
                            <textarea name="current_medications" rows="2" placeholder="e.g. Metformin 500mg" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('current_medications', $patient->current_medications) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Past Surgeries</label>
                            <textarea name="past_surgeries" rows="2" placeholder="e.g. Appendectomy (2020)" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('past_surgeries', $patient->past_surgeries) }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Family Medical History</label>
                        <textarea name="family_history" rows="2" placeholder="e.g. Father: Heart disease, Mother: Diabetes" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('family_history', $patient->family_history) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Insurance Information --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Insurance Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Insurance Provider</label>
                        <input type="text" name="insurance_provider" value="{{ old('insurance_provider', $patient->insurance_provider) }}" placeholder="e.g. MetLife, Green Delta" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Policy Number</label>
                        <input type="text" name="insurance_policy_number" value="{{ old('insurance_policy_number', $patient->insurance_policy_number) }}" placeholder="e.g. POL-12345678" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
            </div>

            {{-- Lifestyle --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em]">Lifestyle</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Smoking Status</label>
                        <select name="smoking_status" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select</option>
                            <option value="never" {{ old('smoking_status', $patient->smoking_status) == 'never' ? 'selected' : '' }}>Never</option>
                            <option value="former" {{ old('smoking_status', $patient->smoking_status) == 'former' ? 'selected' : '' }}>Former</option>
                            <option value="current" {{ old('smoking_status', $patient->smoking_status) == 'current' ? 'selected' : '' }}>Current</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alcohol Status</label>
                        <select name="alcohol_status" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select</option>
                            <option value="none" {{ old('alcohol_status', $patient->alcohol_status) == 'none' ? 'selected' : '' }}>None</option>
                            <option value="occasional" {{ old('alcohol_status', $patient->alcohol_status) == 'occasional' ? 'selected' : '' }}>Occasional</option>
                            <option value="moderate" {{ old('alcohol_status', $patient->alcohol_status) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="heavy" {{ old('alcohol_status', $patient->alcohol_status) == 'heavy' ? 'selected' : '' }}>Heavy</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Activity Level</label>
                        <select name="activity_level" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select</option>
                            <option value="sedentary" {{ old('activity_level', $patient->activity_level) == 'sedentary' ? 'selected' : '' }}>Sedentary</option>
                            <option value="light" {{ old('activity_level', $patient->activity_level) == 'light' ? 'selected' : '' }}>Light</option>
                            <option value="moderate" {{ old('activity_level', $patient->activity_level) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="active" {{ old('activity_level', $patient->activity_level) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="very_active" {{ old('activity_level', $patient->activity_level) == 'very_active' ? 'selected' : '' }}>Very Active</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lifestyle Notes</label>
                    <textarea name="lifestyle_notes" rows="2" placeholder="e.g. Vegetarian diet, regular exercise" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('lifestyle_notes', $patient->lifestyle_notes) }}</textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('patient.dashboard') }}" class="text-gray-400 font-bold text-sm hover:text-gray-600 transition-colors">Cancel</a>
                <button type="submit" class="bg-teal-600 text-white px-10 py-3 rounded-2xl font-black shadow-xl shadow-teal-100 hover:bg-teal-700 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Save Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
