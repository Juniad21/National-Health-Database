@extends('layouts.doctor')

@section('header_title', 'Doctor Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left Column: Today's Queue -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Today's Patient Queue
            </h3>
            
            @if($queue->count() > 0)
                <div class="space-y-4">
                    @foreach($queue as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm">
                                    #{{ $appointment->token_number }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</h4>
                                    <p class="text-sm text-gray-500">Scheduled: <span class="font-medium text-gray-700">{{ $appointment->time_slot }}</span></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('doctor.patient.view', $appointment->patient_id) }}" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
                                    View Profile
                                </a>
                                <form action="{{ route('doctor.queue.visit', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 border border-transparent text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                                        Mark Visited
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <p>No patients in the queue for today.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Patient Search & Access -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Find Patient</h3>
            
            <form action="{{ route('doctor.dashboard') }}" method="GET" class="mb-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Enter NID or Phone Number" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button type="submit" class="w-full mt-3 bg-gray-800 text-white py-2 rounded-xl hover:bg-gray-900 transition-colors font-medium">Search</button>
            </form>

            @if($patientResults)
                <div class="mt-6 border-t border-gray-100 pt-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Results</h4>
                    @if($patientResults->count() > 0)
                        <div class="space-y-3">
                            @foreach($patientResults as $res)
                                @php
                                    $hasAccess = \App\Models\AccessRequest::where('doctor_id', $doctor->id)->where('patient_id', $res->id)->where('status', 'approved')->exists();
                                    $pendingAccess = \App\Models\AccessRequest::where('doctor_id', $doctor->id)->where('patient_id', $res->id)->where('status', 'pending')->exists();
                                @endphp
                                <div class="p-3 border border-gray-200 rounded-lg flex flex-col gap-2">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $res->first_name }} {{ $res->last_name }}</p>
                                        <p class="text-xs text-gray-500">NID: {{ $res->nid }}</p>
                                    </div>
                                    @if($hasAccess)
                                        <a href="{{ route('doctor.patient.view', $res->id) }}" class="text-center w-full py-1.5 bg-blue-50 text-blue-700 rounded border border-blue-200 hover:bg-blue-100 text-sm font-medium transition-colors">View Profile & History</a>
                                    @elseif($pendingAccess)
                                        <button disabled class="w-full py-1.5 bg-yellow-50 text-yellow-700 rounded border border-yellow-200 text-sm font-medium">Access Requested</button>
                                    @else
                                        <form action="{{ route('doctor.patient.request_access', $res->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full py-1.5 bg-white text-gray-700 rounded border border-gray-300 hover:bg-gray-50 text-sm font-medium transition-colors">Request Access</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No patients found.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
