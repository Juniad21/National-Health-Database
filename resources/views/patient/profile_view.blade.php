@extends('layouts.patient')

@section('header_title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
        
        <div class="relative z-10 w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-4xl font-black border-4 border-white shadow-xl">
            {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
        </div>
        
        <div class="relative z-10 flex-1 text-center md:text-left">
            <h2 class="text-3xl font-black text-gray-800 mb-2">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                <span class="px-4 py-1.5 rounded-full bg-red-100 text-red-700 text-xs font-black border border-red-200 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Blood Group: {{ $patient->blood_group ?? 'N/A' }}
                </span>
                <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black border border-emerald-200">
                    NID: {{ $patient->nid }}
                </span>
            </div>
        </div>

        <div class="relative z-10 flex gap-4">
            <a href="{{ route('patient.profile.edit') }}" class="bg-gray-800 text-white px-8 py-3 rounded-2xl font-black shadow-lg hover:bg-black transition-all transform hover:-translate-y-1">
                Edit Profile
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Basic Details -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Contact & Basic</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Date of Birth</p>
                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d M, Y') }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Years)</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Gender</p>
                        <p class="font-bold text-gray-800 capitalize">{{ $patient->gender ?? 'Not Specified' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Phone</p>
                        <p class="font-bold text-gray-800">{{ $patient->phone }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Email</p>
                        <p class="font-bold text-gray-800">{{ $patient->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Address</p>
                        <p class="font-bold text-gray-800 leading-relaxed">{{ $patient->address ?? 'No address provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Insurance Info -->
            <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-100 text-white">
                <h3 class="text-sm font-black text-indigo-200 uppercase tracking-widest mb-6">Insurance Policy</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-black text-indigo-300 uppercase tracking-tighter mb-1">Provider</p>
                        <p class="font-black text-lg">{{ $patient->insurance_provider ?? 'No Insurance' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-indigo-300 uppercase tracking-tighter mb-1">Policy Number</p>
                        <p class="font-mono font-bold">{{ $patient->insurance_policy_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Health Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Medical Overview -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-800 mb-8 flex items-center gap-2">
                    <div class="w-2 h-6 bg-indigo-600 rounded-full"></div>
                    Medical Overview
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Allergies</h4>
                        <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100 text-rose-800 text-sm font-bold min-h-[60px]">
                            {{ $patient->allergies ?? 'No known allergies' }}
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Medical Conditions</h4>
                        <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100 text-amber-800 text-sm font-bold min-h-[60px]">
                            {{ $patient->medical_conditions ?? 'No conditions reported' }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Current Medications</h4>
                        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 text-blue-800 text-sm font-bold min-h-[60px]">
                            {{ $patient->current_medications ?? 'None' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surgical & Family History -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="space-y-8">
                    <div>
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Past Surgeries</h3>
                        <p class="text-gray-700 leading-relaxed font-medium bg-gray-50 p-6 rounded-3xl">
                            {{ $patient->past_surgeries ?? 'No surgical history reported' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Family History</h3>
                        <p class="text-gray-700 leading-relaxed font-medium bg-gray-50 p-6 rounded-3xl">
                            {{ $patient->family_history ?? 'No family history provided' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Lifestyle -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-800 mb-8 flex items-center gap-2">
                    <div class="w-2 h-6 bg-teal-500 rounded-full"></div>
                    Lifestyle & Habits
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-4 bg-teal-50 rounded-2xl border border-teal-100">
                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-tighter mb-1">Smoking</p>
                        <p class="font-black text-gray-800 capitalize">{{ $patient->smoking_status ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-teal-50 rounded-2xl border border-teal-100">
                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-tighter mb-1">Alcohol</p>
                        <p class="font-black text-gray-800 capitalize">{{ $patient->alcohol_status ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-teal-50 rounded-2xl border border-teal-100">
                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-tighter mb-1">Activity</p>
                        <p class="font-black text-gray-800 capitalize">{{ str_replace('_', ' ', $patient->activity_level) ?? 'N/A' }}</p>
                    </div>
                </div>

                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Lifestyle Notes</h4>
                    <p class="text-gray-600 text-sm italic border-l-4 border-teal-200 pl-4 py-2">
                        {{ $patient->lifestyle_notes ?? 'No additional lifestyle notes.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
