@extends('layouts.patient')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Emergency Case #{{ $emergency->id }}</h1>
                <p class="text-gray-600">Created on {{ $emergency->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @php
                    $statusColors = [
                        'Sent' => 'bg-gray-100 text-gray-600',
                        'Reviewing' => 'bg-blue-100 text-blue-600',
                        'Accepted' => 'bg-indigo-100 text-indigo-600',
                        'Ambulance Assigned' => 'bg-purple-100 text-purple-600',
                        'On The Way' => 'bg-yellow-100 text-yellow-600',
                        'Arrived' => 'bg-orange-100 text-orange-600',
                        'Resolved' => 'bg-green-100 text-green-600',
                        'Cancelled' => 'bg-red-100 text-red-600',
                        'Rejected' => 'bg-red-100 text-red-600',
                    ];
                    $color = $statusColors[$emergency->status] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="{{ $color }} px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                    {{ $emergency->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Main Info -->
            <div class="md:col-span-2 space-y-6">
                <!-- Status Timeline (Simplified) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6">Status Update</h3>
                    <div class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                        <div class="relative">
                            <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-green-500 border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-bold text-gray-800">Emergency Triggered</p>
                            <p class="text-xs text-gray-500">{{ $emergency->created_at->format('h:i A') }}</p>
                        </div>
                        
                        @if($emergency->accepted_at)
                        <div class="relative">
                            <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-indigo-500 border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-bold text-gray-800">Hospital Accepted</p>
                            <p class="text-xs text-gray-500">{{ $emergency->accepted_at->format('h:i A') }} by {{ $emergency->hospital->name }}</p>
                        </div>
                        @endif

                        @if($emergency->status === 'Ambulance Assigned' || $emergency->ambulance_id)
                        <div class="relative">
                            <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-purple-500 border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-bold text-gray-800">Ambulance Assigned</p>
                            <p class="text-xs text-gray-500">A team has been dispatched to your location.</p>
                        </div>
                        @endif

                        @if($emergency->status === 'Resolved')
                        <div class="relative">
                            <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-green-600 border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-bold text-gray-800">Resolved</p>
                            <p class="text-xs text-gray-500">{{ $emergency->resolved_at->format('h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Emergency Details -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Case Details</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Type</p>
                            <p class="text-sm text-gray-800">{{ $emergency->emergency_type }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Severity</p>
                            <span class="text-sm font-bold text-red-600 capitalize">{{ $emergency->severity }}</span>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Symptoms reported</p>
                            <p class="text-sm text-gray-800 bg-gray-50 p-3 rounded-lg">{{ $emergency->symptoms ?: 'No additional notes provided.' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Contact</p>
                            <p class="text-sm text-gray-800">{{ $emergency->contact_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Guardian</p>
                            <p class="text-sm text-gray-800">{{ $emergency->guardian_contact ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Cards -->
            <div class="space-y-6">
                <!-- Location Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Reported Location</h3>
                    <div class="aspect-video bg-gray-100 rounded-xl mb-4 flex items-center justify-center relative overflow-hidden">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $emergency->latitude }},{{ $emergency->longitude }}&zoom=15&size=400x250&markers=color:red%7C{{ $emergency->latitude }},{{ $emergency->longitude }}&key=YOUR_API_KEY" alt="Map" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gray-200 flex items-center justify-center bg-opacity-50">
                             <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">GPS Coordinates:</p>
                    <p class="text-xs font-mono text-gray-800 bg-gray-50 p-2 rounded">{{ $emergency->latitude }}, {{ $emergency->longitude }}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $emergency->latitude }},{{ $emergency->longitude }}" target="_blank" class="mt-4 block w-full text-center text-sm font-bold text-indigo-600 hover:text-indigo-700 py-2 border border-indigo-100 rounded-xl">Open in Google Maps</a>
                </div>

                <!-- Assigned Staff -->
                @if($emergency->hospital_id)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Response Team</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Hospital</p>
                                <p class="text-sm font-bold text-gray-800">{{ $emergency->hospital->name }}</p>
                            </div>
                        </div>

                        @if($emergency->assigned_doctor_id)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Doctor Assigned</p>
                                <p class="text-sm font-bold text-gray-800">Dr. {{ $emergency->doctor->first_name }} {{ $emergency->doctor->last_name }}</p>
                            </div>
                        </div>
                        @endif

                        @if($emergency->ambulance_id)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase">Ambulance Team</p>
                                <p class="text-sm font-bold text-gray-800">Unit {{ $emergency->ambulance->ambulance_code }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
