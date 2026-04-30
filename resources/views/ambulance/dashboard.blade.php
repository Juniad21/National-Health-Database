@extends('layouts.ambulance')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Responder Dashboard</h1>
        <p class="text-gray-600">Manage your assigned emergency trips and status updates.</p>
    </div>

    <!-- Assigned Cases -->
    <div class="space-y-6">
        <h3 class="text-lg font-bold text-gray-800">Current Assignments</h3>
        @forelse($assignedEmergencies as $emergency)
        <div class="bg-white rounded-2xl shadow-sm border border-purple-100 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">{{ $emergency->status }}</span>
                            <span class="text-xs text-gray-400 font-medium">Incident #{{ $emergency->id }}</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</h2>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $emergency->address ?: 'Location Coordinates provided' }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $emergency->contact_number }}</span>
                            </div>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-xl">
                            <p class="text-xs text-purple-400 font-bold uppercase mb-1">Medical Brief</p>
                            <p class="text-sm text-purple-900 font-medium">{{ $emergency->emergency_type }}: {{ $emergency->symptoms }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 w-full md:w-64">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $emergency->latitude }},{{ $emergency->longitude }}" target="_blank" class="flex items-center justify-center space-x-2 bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-indigo-100">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 7m0 10V7"/></svg>
                             <span>Navigate</span>
                        </a>
                        
                        <form action="{{ route('ambulance.emergency.status', $emergency->id) }}" method="POST" class="space-y-2">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-purple-200 text-sm font-bold text-purple-900">
                                <option value="">Update Trip Status</option>
                                <option value="On The Way" {{ $emergency->status === 'On The Way' ? 'selected' : '' }}>On The Way</option>
                                <option value="Arrived" {{ $emergency->status === 'Arrived' ? 'selected' : '' }}>Arrived at Patient</option>
                                <option value="Patient Picked Up" {{ $emergency->status === 'Patient Picked Up' ? 'selected' : '' }}>Patient Picked Up</option>
                                <option value="Reached Hospital" {{ $emergency->status === 'Reached Hospital' ? 'selected' : '' }}>Reached Hospital</option>
                                <option value="Completed">Mark Completed / Resolved</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-12 text-center rounded-2xl border border-dashed border-gray-200">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-gray-500 font-medium">No active emergencies assigned to you.</p>
        </div>
        @endforelse
    </div>

    <!-- Past Trips -->
    <div class="mt-12 space-y-6">
        <h3 class="text-lg font-bold text-gray-800">Recent Completed Trips</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Incident</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Patient</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Destination</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($completedEmergencies as $ce)
                    <tr>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">#{{ $ce->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ce->patient->first_name }} {{ $ce->patient->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ce->hospital->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ $ce->updated_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">{{ $ce->status }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
