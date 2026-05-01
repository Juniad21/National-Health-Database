@extends('layouts.hospital')

@section('content')
<div class="p-6" x-data="{ filter: 'All' }">
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
            $activeCount = $emergencies->whereNotIn('status', ['Resolved', 'Cancelled', 'Rejected', 'Sent'])->count();
            $sentCount = $emergencies->where('status', 'Sent')->count();
        @endphp
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 cursor-pointer hover:border-red-200 transition-all" @click="filter = 'Sent'">
            <p class="text-xs text-gray-400 uppercase font-bold mb-1">Incoming SOS</p>
            <p class="text-3xl font-black text-red-600">{{ $sentCount }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 cursor-pointer hover:border-indigo-200 transition-all" @click="filter = 'Accepted'">
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

    <!-- Urgent Alerts Section -->
    @if($sentCount > 0)
    <div class="mb-10">
        <h3 class="text-lg font-black text-red-600 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
            Urgent SOS Alerts
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($emergencies->where('status', 'Sent') as $emergency)
                <div class="bg-white rounded-2xl border-2 border-red-100 shadow-lg shadow-red-50 p-5 hover:border-red-500 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold">
                                {{ substr($emergency->patient->first_name, 0, 1) }}{{ substr($emergency->patient->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-black text-gray-800">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</h4>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">{{ $emergency->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-tighter">
                            {{ $emergency->severity }}
                        </span>
                    </div>
                    <div class="space-y-2 mb-6">
                        <p class="text-sm font-bold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $emergency->emergency_type }}
                        </p>
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $emergency->contact_number }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('hospital.emergencies.accept', $emergency->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-red-600 text-white font-black text-xs rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-100">ACCEPT SOS</button>
                        </form>
                        <a href="{{ route('hospital.emergencies.view', $emergency->id) }}" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold text-xs rounded-xl hover:bg-gray-200 transition-all text-center">DETAILS</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Active Alerts Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Incident Management</h3>
            <div class="flex items-center space-x-2">
                <button @click="filter = 'All'" :class="filter === 'All' ? 'bg-gray-800 text-white' : 'bg-gray-50 text-gray-600'" class="text-[10px] font-black px-3 py-1.5 rounded-lg transition-all">ALL</button>
                <button @click="filter = 'Sent'" :class="filter === 'Sent' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-600'" class="text-[10px] font-black px-3 py-1.5 rounded-lg transition-all">NEW</button>
                <button @click="filter = 'Accepted'" :class="filter === 'Accepted' ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600'" class="text-[10px] font-black px-3 py-1.5 rounded-lg transition-all">ACTIVE</button>
                <button @click="filter = 'Ambulance Assigned'" :class="filter === 'Ambulance Assigned' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-600'" class="text-[10px] font-black px-3 py-1.5 rounded-lg transition-all">DISPATCHED</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Severity</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($emergencies as $emergency)
                    <tr class="hover:bg-gray-50 transition-colors" x-show="filter === 'All' || filter === '{{ $emergency->status }}'">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 font-bold text-xs">
                                    {{ substr($emergency->patient->first_name, 0, 1) }}{{ substr($emergency->patient->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $emergency->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-medium text-gray-700">{{ $emergency->emergency_type }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $severityColors = ['low' => 'bg-blue-100 text-blue-600', 'medium' => 'bg-yellow-100 text-yellow-600', 'high' => 'bg-orange-100 text-orange-600', 'critical' => 'bg-red-100 text-red-600'];
                                $sevColor = $severityColors[$emergency->severity] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="{{ $sevColor }} px-2 py-0.5 rounded text-[9px] font-black uppercase">
                                {{ $emergency->severity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'Sent' => 'bg-red-100 text-red-600',
                                    'Accepted' => 'bg-indigo-100 text-indigo-600',
                                    'Ambulance Assigned' => 'bg-purple-100 text-purple-600',
                                    'Resolved' => 'bg-green-100 text-green-600',
                                    'Rejected' => 'bg-gray-100 text-gray-600',
                                ];
                                $color = $statusColors[$emergency->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="text-[10px] font-black {{ $color }} px-2 py-0.5 rounded uppercase">
                                {{ $emergency->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('hospital.emergencies.view', $emergency->id) }}" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 transition-colors uppercase">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No incident records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
