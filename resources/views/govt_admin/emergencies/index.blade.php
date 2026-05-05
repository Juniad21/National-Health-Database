@extends('layouts.govt_admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">National Emergency Monitor</h1>
            <p class="text-gray-600">Centralized tracking of all medical emergencies and hospital response metrics.</p>
        </div>
        <div class="bg-indigo-600 text-white px-4 py-2 rounded-xl flex items-center space-x-2 shadow-lg shadow-indigo-100">
             <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
             <span class="text-sm font-bold">Real-time Feed</span>
        </div>
    </div>

    <!-- National Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Incidents (24h)</p>
            <p class="text-3xl font-black text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Active / Pending</p>
            <p class="text-3xl font-black text-orange-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Success Resolution</p>
            <p class="text-3xl font-black text-green-600">{{ $stats['resolved'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Rejected Cases</p>
            <p class="text-3xl font-black text-red-600">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    <!-- Live Map Placeholder -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-12 overflow-hidden relative" style="height: 400px;">
         <img src="https://maps.googleapis.com/maps/api/staticmap?center=23.8103,90.4125&zoom=7&size=1200x400&key=YOUR_API_KEY" alt="National Map" class="w-full h-full object-cover">
         <div class="absolute inset-0 bg-indigo-900 bg-opacity-20 flex flex-col items-center justify-center">
              <div class="p-6 bg-white bg-opacity-90 backdrop-blur rounded-2xl shadow-2xl text-center border border-white">
                  <h3 class="text-xl font-bold text-gray-800 mb-2">Live Incident Visualization</h3>
                  <p class="text-sm text-gray-600 mb-4">Displaying all active emergency hotspots across the country.</p>
                  <div class="flex items-center justify-center space-x-6">
                      <div class="flex items-center space-x-2">
                          <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                          <span class="text-xs font-bold text-gray-700">Critical</span>
                      </div>
                      <div class="flex items-center space-x-2">
                          <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                          <span class="text-xs font-bold text-gray-700">High</span>
                      </div>
                      <div class="flex items-center space-x-2">
                          <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                          <span class="text-xs font-bold text-gray-700">Medium</span>
                      </div>
                  </div>
              </div>
         </div>
    </div>

    <!-- National Incident Log -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nationwide Incident Log</h3>
            <div class="flex items-center space-x-3">
                 <button class="text-xs font-bold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-xl">Export Report (CSV)</button>
                 <select class="text-xs font-bold border-gray-200 rounded-xl">
                      <option>All Districts</option>
                      <option>Dhaka</option>
                      <option>Chattogram</option>
                 </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Case ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Patient</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Hospital</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Severity</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Wait Time</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" x-data="{ 
                    showDispatchModal: false, 
                    currentEmergencyId: null,
                    selectedHospitals: []
                }">
                    @foreach($emergencies as $emergency)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">#{{ $emergency->id }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</p>
                            <p class="text-xs text-gray-500">Blood: {{ $emergency->patient->blood_group }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($emergency->hospital_id)
                                <p class="text-sm text-gray-800 font-bold">{{ $emergency->hospital->name }}</p>
                            @elseif($emergency->targetHospitals->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($emergency->targetHospitals as $th)
                                        <span class="text-[8px] bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200" title="{{ $th->name }}">
                                            {{ Str::limit($th->name, 10) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400 italic">Pending Assignment</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $emergency->emergency_type }}</td>
                        <td class="px-6 py-4">
                            @php
                                $sevColor = match($emergency->severity) {
                                    'critical' => 'text-red-600 bg-red-50',
                                    'high' => 'text-orange-600 bg-orange-50',
                                    'medium' => 'text-blue-600 bg-blue-50',
                                    default => 'text-gray-600 bg-gray-50',
                                };
                            @endphp
                            <span class="text-[10px] font-black uppercase tracking-tighter px-2 py-1 rounded {{ $sevColor }}">
                                {{ $emergency->severity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold {{ $emergency->status === 'Pending Dispatch' ? 'text-indigo-600' : 'text-gray-800' }}">
                                {{ $emergency->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                             {{ $emergency->created_at->diffInMinutes($emergency->accepted_at ?: now()) }} mins
                        </td>
                        <td class="px-6 py-4">
                            @if($emergency->status === 'Pending Dispatch' || $emergency->status === 'Sent')
                                <button @click="currentEmergencyId = {{ $emergency->id }}; showDispatchModal = true" 
                                    class="text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white px-3 py-1.5 rounded-lg shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                                    {{ $emergency->status === 'Sent' ? 'Update Alert' : 'Send Alert' }}
                                </button>
                            @else
                                <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">No Action</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div x-show="showDispatchModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden" 
             @click.away="showDispatchModal = false">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-black text-gray-800">Targeted Alert Dispatch</h3>
                <button @click="showDispatchModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="'/govt-admin/emergencies/' + currentEmergencyId + '/dispatch'" method="POST" class="p-6">
                @csrf
                <p class="text-xs text-gray-500 mb-4 font-bold uppercase tracking-widest">Select Hospitals to Notify:</p>
                
                <div class="max-h-60 overflow-y-auto space-y-2 mb-6 custom-scrollbar pr-2">
                    @foreach($allHospitals as $hospital)
                    <label class="flex items-center p-3 bg-gray-50 rounded-2xl cursor-pointer hover:bg-indigo-50 transition-colors border border-transparent hover:border-indigo-100">
                        <input type="checkbox" name="hospital_ids[]" value="{{ $hospital->id }}" class="w-5 h-5 text-indigo-600 border-gray-200 rounded-lg focus:ring-indigo-500">
                        <div class="ml-3">
                            <p class="text-sm font-bold text-gray-800">{{ $hospital->name }}</p>
                            <p class="text-[10px] text-gray-400 font-medium">{{ $hospital->address }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                
                <div class="flex gap-3">
                    <button type="button" @click="showDispatchModal = false" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Broadcast Alert</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
