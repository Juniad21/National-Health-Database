@extends('layouts.hospital')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Emergency Alerts Dashboard</h1>
            <p class="text-gray-600">Monitor and respond to incoming SOS requests in real-time.</p>
        </div>
        <div class="flex items-center space-x-2 bg-red-50 text-red-600 px-4 py-2 rounded-xl animate-pulse">
            <span class="w-2 h-2 bg-red-600 rounded-full"></span>
            <span class="text-sm font-bold uppercase tracking-wider">Live Monitoring Active</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $activeCount = $emergencies->whereNotIn('status', ['Resolved', 'Cancelled', 'Rejected'])->count();
            $sentCount = $emergencies->where('status', 'Sent')->count();
        @endphp
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Incoming SOS</p>
            <p class="text-3xl font-black text-red-600">{{ $sentCount }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Active Cases</p>
            <p class="text-3xl font-black text-indigo-600">{{ $activeCount }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Available Ambulances</p>
            <p class="text-3xl font-black text-green-600">4</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">On-Call Doctors</p>
            <p class="text-3xl font-black text-blue-600">8</p>
        </div>
    </div>

    <!-- Active Alerts Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">All Incident Requests</h3>
            <div class="flex items-center space-x-2">
                <button class="text-xs font-semibold bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-100">All</button>
                <button class="text-xs font-semibold bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100">Sent</button>
                <button class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-100">Accepted</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Emergency Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Severity</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($emergencies as $emergency)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
                                    {{ substr($emergency->patient->first_name, 0, 1) }}{{ substr($emergency->patient->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $emergency->patient->blood_group }} | {{ $emergency->contact_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800">{{ $emergency->emergency_type }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $severityColors = [
                                    'low' => 'bg-blue-100 text-blue-600',
                                    'medium' => 'bg-yellow-100 text-yellow-600',
                                    'high' => 'bg-orange-100 text-orange-600',
                                    'critical' => 'bg-red-100 text-red-600',
                                ];
                                $sevColor = $severityColors[$emergency->severity] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="{{ $sevColor }} px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-tight">
                                {{ $emergency->severity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'Sent' => 'bg-gray-100 text-gray-600',
                                    'Accepted' => 'bg-indigo-100 text-indigo-600',
                                    'Ambulance Assigned' => 'bg-purple-100 text-purple-600',
                                    'Resolved' => 'bg-green-100 text-green-600',
                                    'Rejected' => 'bg-red-100 text-red-600',
                                ];
                                $color = $statusColors[$emergency->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="text-xs font-bold {{ $color }} px-2 py-1 rounded">
                                {{ $emergency->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-500">{{ $emergency->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('hospital.emergencies.view', $emergency->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-lg">Details</a>
                                @if($emergency->status === 'Sent')
                                <form action="{{ route('hospital.emergencies.accept', $emergency->id) }}" method="POST" class="m-0 flex items-center">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg h-full">Accept</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No emergency alerts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
