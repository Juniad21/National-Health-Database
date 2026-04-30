@extends('layouts.admin')

@section('header_title', 'Duplicate Record Management')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-lg shadow-blue-200">
        <h3 class="text-2xl font-bold mb-2">Duplicate Detection Engine</h3>
        <p class="text-blue-100 opacity-90 max-w-2xl">
            Our system has identified potential duplicate patient records based on matching <span class="font-semibold text-white">Full Name</span> and <span class="font-semibold text-white">Date of Birth</span>. Please review and merge these records to maintain data integrity.
        </p>
    </div>

    <!-- Potential Duplicates List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($potentialDuplicateGroups as $group)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Matched Criteria</span>
                            <h4 class="text-lg font-bold text-gray-800">{{ $group['criteria'] }}</h4>
                        </div>
                    </div>
                    <span class="px-4 py-1 bg-blue-50 text-blue-600 text-sm font-bold rounded-full border border-blue-100">
                        {{ count($group['patients']) }} Records Found
                    </span>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($group['patients'] as $patient)
                            <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50/50 relative group">
                                <div class="absolute top-4 right-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">ID: #{{ $patient->id }}</span>
                                </div>
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-blue-700 font-bold text-xl shadow-sm">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                        <p class="text-xs text-gray-500 font-medium">{{ $patient->nid }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $patient->phone ?? 'No Phone' }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }}
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="text-center p-2 bg-white rounded-xl border border-gray-100">
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">Appointments</p>
                                        <p class="font-bold text-gray-700">{{ $patient->appointments()->count() }}</p>
                                    </div>
                                    <div class="text-center p-2 bg-white rounded-xl border border-gray-100">
                                        <p class="text-[10px] text-gray-400 uppercase font-bold">Records</p>
                                        <p class="font-bold text-gray-700">{{ $patient->medicalRecords()->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('admin.duplicates.compare', [$group['patients'][0]->id, $group['patients'][1]->id]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-md shadow-blue-100 group">
                            Compare & Merge Records
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
                <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-6">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Duplicates Detected</h3>
                <p class="text-gray-500">The database is clean! All patient records appear to be unique.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
