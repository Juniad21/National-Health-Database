@extends('layouts.govt_admin')

@section('header_title', 'Disease Monitoring & Public Health')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Reports</p>
            <h3 class="text-3xl font-black text-gray-800">{{ number_format($stats['total_reports']) }}</h3>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <span class="text-indigo-500 font-bold">Lifetime</span> platform tracking
            </p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Confirmed Cases</p>
            <h3 class="text-3xl font-black text-emerald-600">{{ number_format($stats['total_confirmed']) }}</h3>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <span class="text-emerald-500 font-bold">Across all</span> districts
            </p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">High-Risk Reports</p>
            <h3 class="text-3xl font-black text-red-600">{{ number_format($stats['high_risk_reports']) }}</h3>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <span class="text-red-500 font-bold">Requires</span> immediate action
            </p>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-black">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Critical Districts</p>
            <h3 class="text-3xl font-black text-black">{{ number_format($stats['critical_districts']) }}</h3>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <span class="text-black font-bold">Containment</span> needed
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Reports Table -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('govt_admin.disease_monitoring.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="md:col-span-2 lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Disease, District, Hospital..." class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Severity</label>
                        <select name="severity" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm">
                            <option value="">All Severities</option>
                            <option value="Low" {{ request('severity') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ request('severity') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ request('severity') == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Critical" {{ request('severity') == 'Critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                        <select name="status" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm">
                            <option value="">All Statuses</option>
                            <option value="New" {{ request('status') == 'New' ? 'selected' : '' }}>New</option>
                            <option value="Monitoring" {{ request('status') == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="lg:col-span-3 flex justify-end">
                        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-black transition-all">Apply Filters</button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Disease & District</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Hospital</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Cases (C/D)</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Severity</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reports as $report)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-800">{{ $report->disease_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $report->district }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-700">{{ $report->hospital_name }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $report->report_date->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <p class="text-sm font-black text-gray-800">{{ $report->confirmed_cases }}</p>
                                        <p class="text-[10px] text-red-500 font-bold">{{ $report->death_cases }} Deaths</p>
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
                                        <form action="{{ route('govt_admin.disease_monitoring.update_status', $report->id) }}" method="POST">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="text-xs bg-gray-50 border-gray-100 rounded-lg p-1 focus:ring-0">
                                                <option value="New" {{ $report->status == 'New' ? 'selected' : '' }}>New</option>
                                                <option value="Monitoring" {{ $report->status == 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                                                <option value="Notice Sent" {{ $report->status == 'Notice Sent' ? 'selected' : '' }}>Notice Sent</option>
                                                <option value="Hospital Alerted" {{ $report->status == 'Hospital Alerted' ? 'selected' : '' }}>Hospital Alerted</option>
                                                <option value="Resolved" {{ $report->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-gray-400 hover:text-indigo-600 transition-colors" title="View Notes: {{ $report->notes }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <p class="text-gray-400 font-medium italic">No disease reports found.</p>
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

        <!-- Sidebar Components -->
        <div class="space-y-6">
            <!-- Alert Panel -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-t-4 border-t-red-600">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Critical Alerts
                </h4>
                <div class="space-y-4">
                    @forelse($alerts as $alert)
                        <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-sm font-black text-red-900">{{ $alert->disease_name }}</p>
                                <span class="text-[9px] font-black bg-red-600 text-white px-1.5 py-0.5 rounded uppercase">{{ $alert->severity_level }}</span>
                            </div>
                            <p class="text-xs text-red-700 font-medium">{{ $alert->district }} • {{ $alert->hospital_name }}</p>
                            <p class="text-xs text-red-900 mt-2 font-bold">{{ $alert->confirmed_cases }} Confirmed Cases</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No critical alerts at this time.</p>
                    @endforelse
                </div>
            </div>

            <!-- District Summary -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4">District Snapshot</h4>
                <div class="space-y-3">
                    @foreach($districtSummary->take(6) as $ds)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $ds->district }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $ds->report_count }} Reports</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-800">{{ $ds->total_confirmed }}</p>
                                <p class="text-[9px] font-black {{ $ds->highest_severity == 'Critical' ? 'text-red-600' : 'text-gray-400' }} uppercase">{{ $ds->highest_severity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Trend Cards (Pseudo Chart) -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4">Latest Trends</h4>
                <div class="space-y-2">
                    @php
                        $maxTrend = $trends->max('total') ?: 1;
                    @endphp
                    @foreach($trends as $trend)
                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px] font-bold text-gray-500 uppercase">
                                <span>{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}</span>
                                <span>{{ $trend->total }} Cases</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ ($trend->total / $maxTrend) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
