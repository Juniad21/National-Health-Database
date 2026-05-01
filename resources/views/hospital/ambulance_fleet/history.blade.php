@extends('layouts.hospital')

@section('header_title', 'Ambulance Mission History')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('hospital.ambulance_fleet.index') }}" class="p-2 hover:bg-white rounded-full transition-all text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">MISSION ARCHIVE</h2>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mission ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Unit</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient Details</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Duration</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Date Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($completedAssignments as $assignment)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-8 py-5 text-xs font-black text-gray-400">#{{ str_pad($assignment->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-indigo-600 tracking-tight">{{ $assignment->ambulance->ambulance_code }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $assignment->ambulance->ambulance_type }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-800 tracking-tight">{{ $assignment->emergency->patient->first_name }} {{ $assignment->emergency->patient->last_name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $assignment->emergency->emergency_type }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($assignment->started_at && $assignment->completed_at)
                                <span class="text-[10px] font-black text-gray-600 bg-gray-100 px-3 py-1 rounded-full uppercase tracking-tighter">
                                    {{ $assignment->started_at->diffInMinutes($assignment->completed_at) }} MINS
                                </span>
                            @else
                                <span class="text-[10px] font-black text-gray-300 uppercase italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800">{{ $assignment->completed_at->format('M d, Y') }}</span>
                                <span class="text-[10px] font-medium text-gray-400">{{ $assignment->completed_at->format('H:i A') }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center">
                            <p class="text-gray-400 font-black text-sm uppercase tracking-widest">No mission history found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
            {{ $completedAssignments->links() }}
        </div>
    </div>
</div>
@endsection
