@extends('layouts.doctor')

@section('header_title', 'Doctor Dashboard')

@section('content')

@if(session('success'))
    <div class="mb-6 px-6 py-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 font-semibold flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ============================================================ -->
    <!-- LEFT/MAIN COLUMN -->
    <!-- ============================================================ -->
    <div class="lg:col-span-2 space-y-6">

        <!-- PENDING APPOINTMENT APPROVALS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                Pending Appointment Approvals
                @if($pendingApprovals->count() > 0)
                    <span class="ml-auto text-xs font-bold bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full">
                        {{ $pendingApprovals->count() }} waiting
                    </span>
                @endif
            </h3>

            @if($pendingApprovals->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingApprovals as $appointment)
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 bg-yellow-50 rounded-2xl border border-yellow-200 gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-yellow-200 text-yellow-800 flex items-center justify-center font-black text-lg flex-shrink-0">
                                    {{ substr($appointment->patient->first_name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-base">
                                        {{ $appointment->patient->first_name ?? 'Unknown' }} {{ $appointment->patient->last_name ?? 'Patient' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        📅 {{ \Carbon\Carbon::parse($appointment->date)->format('D, M d Y') }}
                                        &bull; ⏰ {{ $appointment->time_slot }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        🏥 {{ $appointment->hospital->name ?? 'Unknown Hospital' }}
                                    </p>
                                    @if($appointment->booking_id)
                                        <p class="text-xs text-gray-400 mt-0.5">Booking: {{ $appointment->booking_id }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <form action="{{ route('doctor.appointment.approve', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-5 py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 transition-colors shadow-sm">
                                        ✓ Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-400 font-medium">No pending appointment approvals.</p>
                    <p class="text-xs text-gray-400 mt-1">When a patient books with you, they'll appear here.</p>
                </div>
            @endif
        </div>

        <!-- TODAY'S WAITING QUEUE -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Today's Approved Queue
                @if($waitingQueue->count() > 0)
                    <span class="ml-auto text-xs font-bold bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full">
                        {{ $waitingQueue->count() }} in queue
                    </span>
                @endif
            </h3>

            @if($waitingQueue->count() > 0)
                <div class="space-y-3">
                    @foreach($waitingQueue as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-black text-base border-2 border-white shadow-sm flex-shrink-0">
                                    {{ $appointment->token_number ?? '#' }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">
                                        {{ $appointment->patient->first_name ?? 'Unknown' }} {{ $appointment->patient->last_name ?? 'Patient' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        Scheduled: <span class="font-semibold text-gray-700">{{ $appointment->time_slot }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $appointment->booking_id }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 flex-shrink-0">
                                <a href="{{ route('doctor.patient.view', $appointment->patient_id) }}"
                                    class="px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
                                    View Profile
                                </a>
                                {{-- Call Patient → goes to consultation page directly --}}
                                <form action="{{ route('doctor.queue.visit', $appointment->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold transition-colors">
                                        📞 Call & Consult
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-gray-400 font-medium">Queue is clear — no approved patients waiting.</p>
                    <p class="text-xs text-gray-400 mt-1">Approve a pending appointment to add patients to the queue.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- RIGHT COLUMN: Patient Search -->
    <!-- ============================================================ -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Find Patient</h3>
            <p class="text-xs text-gray-400 mb-4">Search by NID or phone number to view records or request access.</p>

            <form action="{{ route('doctor.dashboard') }}" method="GET" class="mb-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Enter NID or Phone Number"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit"
                    class="w-full mt-3 bg-gray-800 text-white py-2.5 rounded-xl hover:bg-gray-900 transition-colors font-semibold text-sm">
                    Search Patient
                </button>
            </form>

            @if($patientResults)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Results</h4>
                    @if($patientResults->count() > 0)
                        <div class="space-y-3">
                            @foreach($patientResults as $res)
                                @php
                                    $doctor = Auth::user()->doctor;
                                    $hasAccess = \App\Models\AccessRequest::where('doctor_id', $doctor?->id)
                                        ->where('patient_id', $res->id)->where('status', 'approved')->exists();
                                    $pendingAccess = \App\Models\AccessRequest::where('doctor_id', $doctor?->id)
                                        ->where('patient_id', $res->id)->where('status', 'pending')->exists();
                                @endphp
                                <div class="p-3 border border-gray-200 rounded-xl flex flex-col gap-2 hover:border-blue-200 transition-colors">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $res->first_name }} {{ $res->last_name }}</p>
                                        <p class="text-xs text-gray-400">NID: {{ $res->nid }} &bull; Phone: {{ $res->phone }}</p>
                                    </div>
                                    @if($hasAccess)
                                        <a href="{{ route('doctor.patient.view', $res->id) }}"
                                            class="text-center w-full py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 text-sm font-semibold transition-colors">
                                            View Profile & History
                                        </a>
                                    @elseif($pendingAccess)
                                        <button disabled
                                            class="w-full py-2 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200 text-sm font-medium">
                                            Access Requested
                                        </button>
                                    @else
                                        <form action="{{ route('doctor.patient.request_access', $res->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-2 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm font-medium transition-colors">
                                                Request Access
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic text-center py-4">No patients found for "{{ $search }}"</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- REFERRALS WIDGET -->
        <div class="bg-slate-900 rounded-2xl shadow-xl shadow-slate-900/20 p-6 text-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-400">Clinical Referrals</h3>
                <a href="{{ route('doctor.referrals.index') }}" class="text-[10px] font-black text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest">View All</a>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-slate-800 rounded-xl">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wide">Incoming</span>
                    <span class="px-2.5 py-0.5 bg-teal-500/10 text-teal-400 rounded-lg text-xs font-black">{{ $referralStats['received'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-800 rounded-xl">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wide">Sent (Pending)</span>
                    <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-400 rounded-lg text-xs font-black">{{ $referralStats['sent_pending'] }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
