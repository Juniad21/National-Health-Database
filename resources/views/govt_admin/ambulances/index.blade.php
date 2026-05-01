@extends('layouts.govt_admin')

@section('header_title', 'National Ambulance Fleet Monitoring')

@section('content')
<div class="space-y-8">
    
    <!-- National Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm shadow-indigo-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total National Fleet</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Currently Available</p>
                <p class="text-2xl font-black text-emerald-600">{{ $stats['available'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 shadow-sm shadow-red-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Emergency Trips</p>
                <p class="text-2xl font-black text-red-600">{{ $stats['active_trips'] }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <form action="{{ route('govt_admin.ambulances.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Search Hospital</label>
                <input type="text" name="hospital" value="{{ request('hospital') }}" placeholder="Hospital name..." 
                    class="w-full rounded-xl border-gray-100 bg-gray-50 text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Ambulance Type</label>
                <select name="type" class="w-full rounded-xl border-gray-100 bg-gray-50 text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="Basic Life Support" {{ request('type') == 'Basic Life Support' ? 'selected' : '' }}>Basic Life Support</option>
                    <option value="Advanced Life Support" {{ request('type') == 'Advanced Life Support' ? 'selected' : '' }}>Advanced Life Support</option>
                    <option value="ICU Ambulance" {{ request('type') == 'ICU Ambulance' ? 'selected' : '' }}>ICU Ambulance</option>
                    <option value="Neonatal Ambulance" {{ request('type') == 'Neonatal Ambulance' ? 'selected' : '' }}>Neonatal Ambulance</option>
                    <option value="Patient Transport" {{ request('type') == 'Patient Transport' ? 'selected' : '' }}>Patient Transport</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-100 bg-gray-50 text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Assigned" {{ request('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="On The Way" {{ request('status') == 'On The Way' ? 'selected' : '' }}>On The Way</option>
                    <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <button type="submit" class="py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-black hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 uppercase tracking-widest">Filter Results</button>
        </form>
    </div>

    <!-- Monitoring Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Code</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hospital</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vehicle / Type</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Current Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Trip</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ambulances as $ambulance)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-8 py-5">
                            <span class="font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg text-xs">{{ $ambulance->ambulance_code }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-gray-800 tracking-tight">{{ $ambulance->hospital->name }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-gray-700">{{ $ambulance->vehicle_number }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $ambulance->ambulance_type }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $statusColors = [
                                    'Available' => 'bg-emerald-100 text-emerald-600',
                                    'Assigned' => 'bg-blue-100 text-blue-600',
                                    'On The Way' => 'bg-purple-100 text-purple-600',
                                    'At Patient Location' => 'bg-orange-100 text-orange-600',
                                    'Patient Picked Up' => 'bg-indigo-100 text-indigo-600',
                                    'Heading To Hospital' => 'bg-purple-100 text-purple-600',
                                    'Arrived At Hospital' => 'bg-teal-100 text-teal-600',
                                    'Completed' => 'bg-gray-100 text-gray-600',
                                    'Maintenance' => 'bg-amber-100 text-amber-600',
                                    'Out Of Service' => 'bg-red-100 text-red-600',
                                ];
                                $color = $statusColors[$ambulance->current_status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 {{ $color }} rounded-full text-[10px] font-black uppercase tracking-tighter border border-current opacity-90 shadow-sm">{{ $ambulance->current_status }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @if($ambulance->currentAssignment)
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-purple-600">Active Mission</span>
                                <span class="text-[10px] font-bold text-gray-400">Trip #{{ $ambulance->currentAssignment->id }}</span>
                            </div>
                            @else
                            <span class="text-[10px] font-black text-gray-300 uppercase italic">Idle</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($ambulance->current_location_lat)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $ambulance->current_location_lat }},{{ $ambulance->current_location_lng }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-black text-[10px] uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                LIVE GPS
                            </a>
                            @else
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No GPS Data</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-12 text-center">
                            <p class="text-gray-400 font-black text-sm uppercase tracking-widest">No matching ambulances found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $ambulances->links() }}
        </div>
    </div>
</div>
@endsection
