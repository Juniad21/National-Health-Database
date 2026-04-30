@extends('layouts.hospital')

@section('header_title', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('hospital.disease_reports.index') }}" class="flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-indigo-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Reports
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status:</span>
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-widest">{{ $report->status }}</span>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">{{ $report->disease_name }}</h2>
                <p class="text-gray-500 font-medium">{{ $report->district }} • Reported on {{ $report->report_date->format('M d, Y') }}</p>
            </div>
            @php
                $sevColors = [
                    'Low' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'High' => 'bg-red-100 text-red-700 border-red-200',
                    'Critical' => 'bg-black text-white border-black',
                ];
            @endphp
            <div class="{{ $sevColors[$report->severity_level] }} px-6 py-3 rounded-2xl border text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-70">Severity</p>
                <p class="text-xl font-black uppercase">{{ $report->severity_level }}</p>
            </div>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50/30">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Suspected</p>
                <p class="text-2xl font-black text-gray-800">{{ $report->suspected_cases }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 text-emerald-500">Confirmed</p>
                <p class="text-2xl font-black text-emerald-600">{{ $report->confirmed_cases }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 text-blue-500">Recovered</p>
                <p class="text-2xl font-black text-blue-600">{{ $report->recovered_cases }}</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm text-center border-l-4 border-l-red-500">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 text-red-500">Deaths</p>
                <p class="text-2xl font-black text-red-600">{{ $report->death_cases }}</p>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <div>
                <h3 class="text-xs font-black text-indigo-500 uppercase tracking-[0.2em] mb-4">Notes & Observations</h3>
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-gray-700 leading-relaxed">{{ $report->notes ?: 'No additional notes provided for this report.' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Hospital Information</h3>
                    <p class="text-sm font-bold text-gray-800">{{ $report->hospital_name }}</p>
                    <p class="text-xs text-gray-500">Facility ID: #{{ $report->hospital_id }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Submission Trace</h3>
                    <p class="text-sm font-bold text-gray-800">Report #{{ $report->id }}</p>
                    <p class="text-xs text-gray-500">Logged on {{ $report->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
