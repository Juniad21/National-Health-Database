@extends('layouts.hospital')

@section('header_title', 'Disease Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Disease Surveillance</h2>
            <p class="text-gray-500 text-sm">Report new cases and monitor public health trends from your facility.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('hospital.disease_reports.create') }}" 
               class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Report
            </a>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs font-bold text-gray-500 uppercase tracking-widest">
                        <th class="px-6 py-4">Report Date</th>
                        <th class="px-6 py-4">Disease & District</th>
                        <th class="px-6 py-4 text-center">Cases (C/D)</th>
                        <th class="px-6 py-4 text-center">Severity</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $report->report_date->format('M d, Y') }}</p>
                                <p class="text-[10px] text-gray-400 font-mono italic">Submitted {{ $report->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $report->disease_name }}</p>
                                <p class="text-xs text-indigo-500 font-bold uppercase tracking-widest">{{ $report->district }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col">
                                    <span class="text-sm font-black text-gray-800">{{ $report->confirmed_cases }}</span>
                                    <span class="text-[9px] font-black text-red-500 uppercase">{{ $report->death_cases }} Deaths</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $sevColors = [
                                        'Low' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'Medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'High' => 'bg-red-100 text-red-700 border-red-200',
                                        'Critical' => 'bg-black text-white border-black',
                                    ];
                                @endphp
                                <span class="{{ $sevColors[$report->severity_level] }} text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-tighter border">
                                    {{ $report->severity_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'New' => 'bg-gray-100 text-gray-600',
                                        'Monitoring' => 'bg-indigo-50 text-indigo-600',
                                        'Hospital Alerted' => 'bg-red-50 text-red-600',
                                        'Resolved' => 'bg-emerald-50 text-emerald-600',
                                    ];
                                @endphp
                                <span class="{{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-600' }} text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-widest">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('hospital.disease_reports.show', $report->id) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <p class="text-gray-400 font-medium italic">You haven't submitted any disease reports yet.</p>
                                <a href="{{ route('hospital.disease_reports.create') }}" class="text-indigo-600 font-bold text-sm mt-2 inline-block underline">Submit your first report</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
