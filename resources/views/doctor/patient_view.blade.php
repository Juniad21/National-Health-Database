@extends('layouts.doctor')

@section('header_title', 'Patient Profile')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('doctor.dashboard') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Dashboard
            </a>
            <a href="{{ route('doctor.consultation', $patient->id) }}"
                class="px-5 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Consultation
            </a>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-start gap-6">
            <div
                class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-2xl font-bold border-4 border-white shadow-md">
                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                    <span
                        class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">{{ $patient->blood_group }}</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4">
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">NID</span><span
                            class="font-medium">{{ $patient->nid }}</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Age/DOB</span><span
                            class="font-medium">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs
                            ({{ $patient->date_of_birth }})</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Gender</span><span
                            class="font-medium capitalize">{{ $patient->gender }}</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Contact</span><span
                            class="font-medium">{{ $patient->phone }}</span></div>
                </div>
            </div>
        </div>

        <!-- Medical History Timeline -->
        <h3 class="font-bold text-gray-800 text-lg mt-8 mb-4">Past Medical History</h3>

        @if($records->isEmpty())
            <div class="text-center py-12 bg-white rounded-2xl border border-gray-200 border-dashed">
                <p class="text-gray-500">No medical history available for this patient.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($records as $type => $group)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
                            @if($type === 'prescription')
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">Prescriptions & Consultations</h4>
                            @elseif($type === 'lab_report')
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">Laboratory Reports</h4>
                            @else
                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $type) }}</h4>
                            @endif
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($group as $record)
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-bold text-gray-800">{{ $record->diagnosis }}</h5>
                                        <span
                                            class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 mt-3 border border-gray-100">
                                        {!! nl2br(e($record->medications_or_results)) !!}
                                    </div>
                                    <p class="text-xs text-gray-400 mt-3 font-medium">Recorded by: Dr. {{ $record->doctor->first_name }}
                                        {{ $record->doctor->last_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection