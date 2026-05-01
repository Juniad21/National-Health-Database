@extends('layouts.govt_admin')

@section('header_title', 'Verify Doctor Credentials')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('govt_admin.doctors.index') }}" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
            Back to Directory
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Profile Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex items-start gap-6">
                    <div class="w-24 h-24 rounded-2xl bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-100">
                        @if($profile->profile_photo)
                            <img src="{{ asset('storage/' . $profile->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-800">Dr. {{ $profile->full_name }}</h2>
                        <p class="text-indigo-600 font-bold">{{ $profile->specialization }} &bull; {{ $profile->designation }}</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <span class="px-3 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-500 border border-gray-100">License: {{ $profile->license_number }}</span>
                            <span class="px-3 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-500 border border-gray-100">Exp: {{ $profile->years_of_experience }} Years</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 md:grid-cols-3 gap-8 pt-8 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Phone</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Email</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">BMDC Reg.</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->license_number }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Medical College</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->medical_college ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Expiry Date</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->license_expiry_date ? $profile->license_expiry_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Gender</p>
                        <p class="text-sm font-bold text-gray-700">{{ $profile->gender ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Credentials & Biography</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase mb-2">Qualifications</p>
                        <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                            {{ $profile->qualifications }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase mb-2">Biography</p>
                        <p class="text-sm text-gray-700 leading-relaxed italic">
                            {{ $profile->biography ?: 'No biography provided.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Sidebar -->
        <div class="space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-50 pb-4">Verification Action</h3>
                
                <form action="{{ route('govt_admin.doctors.verify', $profile->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Update Status</label>
                        <select name="status" class="w-full rounded-xl border-gray-200 text-sm font-bold">
                            <option value="Pending" {{ $profile->verification_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Verified" {{ $profile->verification_status == 'Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Rejected" {{ $profile->verification_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Needs Review" {{ $profile->verification_status == 'Needs Review' ? 'selected' : '' }}>Needs Review</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Admin Notes (Private)</label>
                        <textarea name="admin_notes" rows="4" class="w-full rounded-xl border-gray-200 text-sm" placeholder="Add internal notes about the license verification, missing documents, etc.">{{ $profile->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gray-800 text-white font-bold rounded-2xl hover:bg-gray-900 transition-all shadow-xl shadow-gray-200 transform hover:-translate-y-1">
                        Update Verification
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100">
                <h4 class="text-xs font-black text-gray-400 uppercase mb-4 tracking-widest">Affiliation Info</h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">System Hospital</p>
                        <p class="text-xs font-bold text-gray-700">{{ $profile->hospital->name ?? 'None' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Reported Facility</p>
                        <p class="text-xs font-bold text-gray-700">{{ $profile->hospital_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
