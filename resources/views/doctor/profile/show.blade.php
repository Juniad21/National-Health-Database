@extends('layouts.doctor')

@section('header_title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- HEADER / SUMMARY CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
        <!-- Verification Ribbon -->
        <div class="absolute top-6 right-8">
            @php
                $statusColors = [
                    'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'Verified' => 'bg-green-100 text-green-700 border-green-200',
                    'Rejected' => 'bg-red-100 text-red-700 border-red-200',
                    'Needs Review' => 'bg-orange-100 text-orange-700 border-orange-200',
                ];
                $colorClass = $statusColors[$profile->verification_status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            @endphp
            <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest border {{ $colorClass }}">
                {{ $profile->verification_status }}
            </span>
        </div>

        <div class="p-8 md:p-12 flex flex-col md:flex-row gap-8 items-start">
            <div class="w-32 h-32 rounded-3xl bg-blue-50 border-4 border-white shadow-lg overflow-hidden flex-shrink-0">
                @if($profile->profile_photo)
                    <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-blue-300">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
            </div>

            <div class="space-y-4 flex-1">
                <div>
                    <h1 class="text-3xl font-black text-gray-800">Dr. {{ $profile->full_name }}</h1>
                    <p class="text-blue-600 font-bold tracking-tight">{{ $profile->specialization }} &bull; {{ $profile->designation ?? 'Medical Professional' }}</p>
                </div>

                <div class="flex flex-wrap gap-4 pt-2">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Experience</span>
                        <span class="text-sm font-bold text-gray-700">{{ $profile->years_of_experience }} Years</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Hospital</span>
                        <span class="text-sm font-bold text-gray-700">{{ $profile->hospital->name ?? $profile->hospital_name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <a href="{{ route('doctor.profile.edit') }}" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    @if($profile->admin_notes)
    <div class="bg-orange-50 border border-orange-100 p-6 rounded-2xl flex gap-4">
        <svg class="w-6 h-6 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div>
            <p class="text-sm font-bold text-orange-800 uppercase tracking-widest mb-1">Admin Verification Notes</p>
            <p class="text-sm text-orange-700 italic">"{{ $profile->admin_notes }}"</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Details -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-3">Credentials</h3>
                
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">BMDC/License</p>
                    <p class="text-sm font-bold text-gray-700">{{ $profile->license_number }}</p>
                    @if($profile->license_expiry_date)
                        <p class="text-[10px] text-red-400 mt-1">Expires: {{ $profile->license_expiry_date->format('M d, Y') }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Qualifications</p>
                    <p class="text-sm font-bold text-gray-700">{{ $profile->qualifications }}</p>
                </div>

                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Medical College</p>
                    <p class="text-sm font-bold text-gray-700">{{ $profile->medical_college ?? 'N/A' }}</p>
                </div>

                <div class="pt-4">
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Languages</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach(explode(', ', $profile->languages_spoken ?? 'English') as $lang)
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold">{{ $lang }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-6">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-3">Consultation</h3>
                
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Base Fee</p>
                    <p class="text-xl font-black text-blue-600">৳{{ number_format($profile->consultation_fee, 2) }}</p>
                </div>

                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Type</p>
                    <p class="text-sm font-bold text-gray-700">{{ $profile->consultation_type }}</p>
                </div>

                <div class="pt-4 border-t border-gray-50">
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Emergency Status</p>
                    @if($profile->emergency_availability)
                        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">Active Responder</span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest">Not Available</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Details -->
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">About / Biography</h3>
                <p class="text-gray-600 leading-relaxed italic">
                    {{ $profile->biography ?: 'No biography provided yet.' }}
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Schedule & Availability</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-3">Available Days</p>
                        <div class="flex flex-wrap gap-2">
                            @php $allDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; @endphp
                            @php $availDays = explode(', ', $profile->available_days ?? ''); @endphp
                            @foreach($allDays as $day)
                                <span class="px-3 py-1.5 rounded-xl text-[11px] font-bold border {{ in_array($day, $availDays) ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-300 border-gray-100 opacity-50' }}">
                                    {{ $day }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-3">Time Slots</p>
                        <p class="text-sm font-bold text-gray-700">
                            {{ $profile->available_time_slots ?: 'Contact for details' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-50 pb-3">Services</h3>
                    <ul class="space-y-2">
                        @foreach(explode(', ', $profile->services_offered ?? '') as $service)
                            @if($service)
                                <li class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                    {{ $service }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-50 pb-3">Awards</h3>
                    <ul class="space-y-2">
                        @foreach(explode(', ', $profile->awards_certifications ?? '') as $award)
                            @if($award)
                                <li class="flex items-start gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    {{ $award }}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
