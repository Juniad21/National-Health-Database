@extends('layouts.govt_admin')

@section('header_title', 'Access Logs & Audit Trail')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">System Audit Trail</h2>
            <p class="text-gray-500 text-sm">Monitor all system activities and administrative actions across the platform.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('govt_admin.audit_logs.export') }}?{{ http_build_query(request()->all()) }}" 
               class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('govt_admin.audit_logs') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="User, IP, Action, or Module..." 
                           class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute right-3 top-2.5 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Severity</label>
                <select name="severity" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Severities</option>
                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Module</label>
                <select name="module" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Modules</option>
                    <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>Authentication</option>
                    <option value="billing" {{ request('module') == 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="medical" {{ request('module') == 'medical' ? 'selected' : '' }}>Medical Records</option>
                    <option value="hospital" {{ request('module') == 'hospital' ? 'selected' : '' }}>Hospital Ops</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">From</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-800 text-white font-bold py-2.5 rounded-xl text-sm hover:bg-black transition-all">Filter</button>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Timestamp</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">User & Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Action</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Module</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Severity</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">IP Address</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800">{{ $log->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $log->created_at->format('H:i:s') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                        {{ $log->user ? substr($log->user->first_name, 0, 1) : 'S' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</p>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-500">{{ $log->role }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 font-medium">{{ $log->action }}</p>
                                <p class="text-xs text-gray-400 italic truncate max-w-[200px]">{{ $log->description }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                                    {{ $log->module ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $severityColors = [
                                        'low' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'medium' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'high' => 'bg-red-100 text-red-700 border-red-200',
                                        'critical' => 'bg-black text-white border-black',
                                    ];
                                    $color = $severityColors[strtolower($log->severity)] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="{{ $color }} text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-tighter border">
                                    {{ $log->severity }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-xs text-gray-400">{{ $log->ip_address }}</code>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <p class="text-gray-400 font-medium italic">No audit logs found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
