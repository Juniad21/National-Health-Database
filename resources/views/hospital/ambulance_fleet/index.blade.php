@extends('layouts.hospital')

@section('header_title', 'Ambulance Fleet Management')

@section('content')
<div class="space-y-8" x-data="{ showAddModal: false, editingAmbulance: null }">
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Fleet</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Available</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['available'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">On Duty</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['assigned'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Unavailable</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['unavailable'] }}</p>
            </div>
        </div>
    </div>

    <!-- Active Assignments -->
    @if($activeAssignments->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-purple-50/50">
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Active Emergency Missions
            </h3>
            <span class="text-[10px] font-black text-purple-400 uppercase tracking-widest">In-Progress Operations</span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($activeAssignments as $assignment)
                <div class="bg-white border-2 border-purple-50 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all relative group">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-2.5 py-1 bg-purple-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-md shadow-purple-100">{{ $assignment->status }}</span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit {{ $assignment->ambulance->ambulance_code }}</span>
                            </div>
                            
                            <h4 class="text-xl font-black text-gray-800 tracking-tight mb-2">{{ $assignment->emergency->patient->first_name }} {{ $assignment->emergency->patient->last_name }}</h4>
                            
                            <div class="space-y-2 mb-6 text-xs font-bold text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span class="truncate">{{ $assignment->pickup_address ?: 'Coordinates only' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Dispatch {{ $assignment->assigned_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-56 bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mission Phase</label>
                            <form action="{{ route('hospital.ambulance_fleet.assignment.status', $assignment->id) }}" method="POST" class="space-y-2">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 bg-white text-[11px] font-black text-gray-700 uppercase tracking-widest focus:ring-purple-500 py-2.5">
                                    <option value="Accepted" {{ $assignment->status == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="Completed">Complete Mission</option>
                                    <option value="Cancelled">Cancel Mission</option>
                                </select>
                            </form>
                            <a href="{{ route('hospital.emergencies.view', $assignment->emergency_alert_id) }}" class="block w-full mt-2 py-2 bg-white border border-purple-200 text-purple-600 text-center rounded-xl text-[10px] font-black hover:bg-purple-600 hover:text-white transition-all shadow-sm">COMMAND CENTER</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Fleet Management Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-black text-gray-800 text-lg flex items-center gap-2 uppercase tracking-tight">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Unit Registry
            </h3>
            <button @click="showAddModal = true; editingAmbulance = null" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-black hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-md shadow-indigo-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                REGISTER UNIT
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Unit Code</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vehicle Details</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Duty Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Mission</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ambulances as $ambulance)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <span class="font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg text-xs">{{ $ambulance->ambulance_code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-800 text-sm tracking-tight">{{ $ambulance->vehicle_number }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $ambulance->ambulance_type }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
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
                            <span class="px-2.5 py-1 {{ $color }} rounded-full text-[10px] font-black uppercase tracking-tighter shadow-sm border border-current opacity-90">{{ $ambulance->current_status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php $currAssignment = $ambulance->assignments()->whereNotIn('status', ['Completed', 'Cancelled'])->latest()->first(); @endphp
                            @if($currAssignment)
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-purple-600 tracking-tight">Mission in Progress</span>
                                <span class="text-[10px] font-bold text-gray-400">ID #{{ $currAssignment->id }}</span>
                            </div>
                            @else
                            <span class="text-[10px] font-black text-gray-300 uppercase italic">Standby</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editingAmbulance = {{ json_encode($ambulance) }}; showAddModal = true" class="p-2 bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-all" title="Edit Unit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                @if($currAssignment)
                                <a href="{{ route('hospital.emergencies.view', $currAssignment->emergency_alert_id) }}" class="p-2 bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white rounded-lg transition-all shadow-sm" title="Command Center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </a>
                                @endif

                                @if($ambulance->current_status !== 'Available')
                                <form action="{{ route('hospital.ambulance_fleet.reset', $ambulance->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg transition-all shadow-sm" title="Mark Available (Back from Duty)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-gray-400 font-black text-sm uppercase tracking-widest">No Units Registered</p>
                            <p class="text-gray-300 text-xs mt-1">Register ambulances to enable emergency response.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Add/Edit Ambulance -->
    <div x-show="showAddModal" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        x-cloak
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-40 backdrop-blur-sm" 
                @click="showAddModal = false; editingAmbulance = null"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div x-show="showAddModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1" x-text="editingAmbulance ? 'UNIT MODIFICATION' : 'NEW UNIT REGISTRATION'"></h3>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Facility Fleet Management</p>
                    </div>
                    <button @click="showAddModal = false; editingAmbulance = null" class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editingAmbulance ? `/hospital/ambulance-fleet/${editingAmbulance.id}` : '{{ route('hospital.ambulance_fleet.store') }}'" method="POST" class="space-y-5">
                    @csrf
                    <template x-if="editingAmbulance">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Unit Code</label>
                            <input type="text" name="ambulance_code" :value="editingAmbulance ? editingAmbulance.ambulance_code : ''" required placeholder="e.g. AMB-101"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Plate Number</label>
                            <input type="text" name="vehicle_number" :value="editingAmbulance ? editingAmbulance.vehicle_number : ''" required placeholder="DHK-METRO-..."
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Unit Classification</label>
                        <select name="ambulance_type" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                            <option value="Basic Life Support" :selected="editingAmbulance \u0026\u0026 editingAmbulance.ambulance_type == 'Basic Life Support'">Basic Life Support</option>
                            <option value="Advanced Life Support" :selected="editingAmbulance \u0026\u0026 editingAmbulance.ambulance_type == 'Advanced Life Support'">Advanced Life Support</option>
                            <option value="ICU Ambulance" :selected="editingAmbulance \u0026\u0026 editingAmbulance.ambulance_type == 'ICU Ambulance'">ICU Ambulance</option>
                            <option value="Neonatal Ambulance" :selected="editingAmbulance \u0026\u0026 editingAmbulance.ambulance_type == 'Neonatal Ambulance'">Neonatal Ambulance</option>
                            <option value="Patient Transport" :selected="editingAmbulance \u0026\u0026 editingAmbulance.ambulance_type == 'Patient Transport'">Patient Transport</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Stretcher Capacity</label>
                            <input type="number" name="capacity" :value="editingAmbulance ? editingAmbulance.capacity : 1" min="1" required
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                        <template x-if="editingAmbulance">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Availability</label>
                                <select name="current_status" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                                    <option value="Available" :selected="editingAmbulance.current_status == 'Available'">Available</option>
                                    <option value="Maintenance" :selected="editingAmbulance.current_status == 'Maintenance'">In Maintenance</option>
                                    <option value="Out Of Service" :selected="editingAmbulance.current_status == 'Out Of Service'">Decommissioned</option>
                                </select>
                            </div>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Assigned Driver</label>
                            <input type="text" name="driver_name" :value="editingAmbulance ? editingAmbulance.driver_name : ''" placeholder="Name"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Driver Contact</label>
                            <input type="text" name="driver_phone" :value="editingAmbulance ? editingAmbulance.driver_phone : ''" placeholder="Phone"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Operational Notes</label>
                        <textarea name="notes" rows="2" x-text="editingAmbulance ? editingAmbulance.notes : ''" placeholder="Medical equipment notes..."
                            class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 focus:border-indigo-500 py-3"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 tracking-widest uppercase text-xs" x-text="editingAmbulance ? 'COMMIT UPDATES' : 'FINALIZE REGISTRATION'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
