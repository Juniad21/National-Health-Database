@extends('layouts.hospital')

@section('header_title', 'Access & Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Audit Trail
        </h3>

        <form action="{{ route('hospital.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Action</label>
                <select name="action" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Role</label>
                <select name="role" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm text-sm text-center">
                    Filter
                </button>
                <a href="{{ route('hospital.logs') }}" class="py-2 px-4 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors shadow-sm text-sm text-center">
                    Clear
                </a>
            </div>
        </form>

        <div class="overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">Timestamp</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">User</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">Action</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">Module</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">Severity</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide">Details</th>
                        <th class="p-4 font-bold text-gray-700 uppercase tracking-wide text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-gray-600 whitespace-nowrap">
                                <div class="font-bold">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'System' }}</div>
                                <div class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">{{ $log->role }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-gray-700">{{ $log->action }}</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                                    {{ $log->module ?? 'General' }}
                                </span>
                            </td>
                            <td class="p-4">
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
                            <td class="p-4 text-gray-600 italic">
                                {{ $log->description ?: '-' }}
                            </td>
                            <td class="p-4 text-gray-400 font-mono text-xs text-right">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                No logs found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection