@extends('layouts.hospital')

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('hospital.emergencies.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition-colors text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Incident #{{ $emergency->id }}</h1>
                    <p class="text-gray-600">Patient: <span class="font-bold">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</span></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
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
                <span class="{{ $color }} px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                    {{ $emergency->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Location & Map -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center justify-between">
                        <span>Incident Location</span>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $emergency->latitude }},{{ $emergency->longitude }}" target="_blank" class="text-xs text-indigo-600 font-bold hover:underline">View in Full Map</a>
                    </h3>
                    <div class="aspect-[21/9] bg-gray-100 rounded-xl overflow-hidden relative border border-gray-100 mb-4">
                         <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $emergency->latitude }},{{ $emergency->longitude }}&zoom=15&size=800x300&markers=color:red%7C{{ $emergency->latitude }},{{ $emergency->longitude }}&key=YOUR_API_KEY" alt="Location Map" class="w-full h-full object-cover">
                         <div class="absolute inset-0 bg-gray-200 flex items-center justify-center bg-opacity-30">
                              <div class="p-3 bg-white rounded-full shadow-lg">
                                  <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                              </div>
                         </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                              <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Coordinates</p>
                              <p class="text-sm font-mono text-gray-700">{{ $emergency->latitude }}, {{ $emergency->longitude }}</p>
                         </div>
                         <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                              <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Reported Address</p>
                              <p class="text-sm text-gray-700 italic">{{ $emergency->address ?: 'Not provided' }}</p>
                         </div>
                    </div>
                </div>

                <!-- Patient & Medical Info -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6">Patient & Medical Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Blood Group</p>
                            <p class="text-sm font-black text-red-600">{{ $emergency->patient->blood_group }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Age / Gender</p>
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->patient->gender }}, {{ \Carbon\Carbon::parse($emergency->patient->date_of_birth)->age }} yrs</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">NID</p>
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->patient->nid }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Contact</p>
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->contact_number }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-3">Incident Description</h4>
                                <div class="p-4 bg-red-50 rounded-xl text-red-800 text-sm italic">
                                    "{{ $emergency->symptoms ?: 'No description provided.' }}"
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-3">Guardian Info</h4>
                                <div class="p-4 bg-gray-50 rounded-xl text-gray-700 text-sm">
                                    {{ $emergency->guardian_contact ?: 'No guardian contact provided.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div class="space-y-8">
                <!-- Response Management -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-6">Response Management</h3>
                    
                    @if($emergency->status === 'Sent')
                    <div class="space-y-3">
                        <form action="{{ route('hospital.emergencies.accept', $emergency->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-100 transition-all">Accept Case</button>
                        </form>
                        <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="w-full bg-white border border-red-100 text-red-600 hover:bg-red-50 font-bold py-3 rounded-xl transition-all">Reject Case</button>
                    </div>
                    @elseif($emergency->status === 'Rejected')
                    <div class="p-4 bg-red-50 text-red-600 rounded-xl">
                        <p class="text-xs font-bold uppercase mb-1">Rejection Reason</p>
                        <p class="text-sm">{{ $emergency->rejection_reason }}</p>
                    </div>
                    @else
                    <!-- Assigned Hospital View -->
                    <div class="space-y-6">
                        <!-- Dispatch Ambulance -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Dispatch Ambulance</label>
                            @if($emergency->assigned_ambulance_id)
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-xl text-purple-600">
                                    <span class="text-sm font-bold">Ambulance Dispatched</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @else
                                <form action="{{ route('hospital.emergencies.dispatch', $emergency->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <select name="ambulance_id" required class="w-full rounded-xl border-gray-200 text-sm">
                                        <option value="">Select Unit</option>
                                        @foreach($ambulances as $amb)
                                            <option value="{{ $amb->id }}">Ambulance Unit #{{ $amb->id }} ({{ $amb->name }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded-xl text-sm">Dispatch Unit</button>
                                </form>
                            @endif
                        </div>

                        <!-- Assign Doctor -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Assign Doctor</label>
                            @if($emergency->assigned_doctor_id)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl text-blue-600">
                                    <span class="text-sm font-bold">Dr. {{ $emergency->doctor->first_name }} {{ $emergency->doctor->last_name }}</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @else
                                <form action="{{ route('hospital.emergencies.assign_doctor', $emergency->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    <select name="doctor_id" required class="w-full rounded-xl border-gray-200 text-sm">
                                        <option value="">Select Doctor</option>
                                        @foreach($doctors as $doc)
                                            <option value="{{ $doc->id }}">Dr. {{ $doc->first_name }} {{ $doc->last_name }} ({{ $doc->specialization }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl text-sm">Assign Doctor</button>
                                </form>
                            @endif
                        </div>

                        <!-- Resolution -->
                        @if($emergency->status !== 'Resolved')
                        <div class="pt-6 border-t border-gray-50">
                            <form action="{{ route('hospital.emergencies.resolve', $emergency->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-green-100">Resolve Case</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Case Log -->
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">System Log</h3>
                    <div class="space-y-4">
                        <div class="flex space-x-3">
                            <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></div>
                            <div>
                                <p class="text-xs text-gray-800 font-bold">SOS Triggered</p>
                                <p class="text-[10px] text-gray-400">{{ $emergency->created_at->format('M d, H:i') }}</p>
                            </div>
                        </div>
                        @if($emergency->accepted_at)
                        <div class="flex space-x-3">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5"></div>
                            <div>
                                <p class="text-xs text-gray-800 font-bold">Case Accepted</p>
                                <p class="text-[10px] text-gray-400">{{ $emergency->accepted_at->format('M d, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($emergency->resolved_at)
                        <div class="flex space-x-3">
                            <div class="w-2 h-2 rounded-full bg-green-600 mt-1.5"></div>
                            <div>
                                <p class="text-xs text-gray-800 font-bold">Case Resolved</p>
                                <p class="text-[10px] text-gray-400">{{ $emergency->resolved_at->format('M d, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Reject Emergency Case</h3>
            <form action="{{ route('hospital.emergencies.reject', $emergency->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection</label>
                    <textarea name="rejection_reason" required rows="3" class="w-full rounded-xl border-gray-200" placeholder="e.g. No beds available, out of vicinity..."></textarea>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-gray-600 font-bold">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl font-bold">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
