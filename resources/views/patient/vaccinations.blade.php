@extends('layouts.patient')

@section('header_title', 'Immunization & Vaccination Records')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-teal-900 px-8 py-6 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white">Vaccination Timeline</h3>
                <p class="text-teal-400 text-sm mt-1">Comprehensive record of all administered and scheduled immunizations.</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-12 h-12 text-teal-700 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L14.586 11l-8.293 8.293A1 1 0 107.707 20.707l9-9a1 1 0 000-1.414l-9-9A1 1 0 007 2z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <div class="p-8">
            @if($vaccinations->isEmpty())
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-gray-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-bold">No immunization records found.</p>
                    <p class="text-gray-400 text-sm">Please consult your doctor to add your vaccination history.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="pb-4 px-4">Vaccine Details</th>
                                <th class="pb-4 px-4">Status</th>
                                <th class="pb-4 px-4">Scheduled Date</th>
                                <th class="pb-4 px-4">Completed Date</th>
                                <th class="pb-4 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($vaccinations as $vaccine)
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="py-5 px-4">
                                        <div class="flex items-center gap-4">
                                            <div class="p-2 {{ $vaccine->status === 'taken' ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }} rounded-xl">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-800">{{ $vaccine->vaccine_name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">Primary Immunization</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        @if($vaccine->status === 'taken')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Administered
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                                <svg class="w-3 h-3 mr-1.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ ucfirst($vaccine->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-4">
                                        <p class="text-sm font-bold text-gray-600">{{ \Carbon\Carbon::parse($vaccine->due_date)->format('M d, Y') }}</p>
                                    </td>
                                    <td class="py-5 px-4">
                                        @if($vaccine->status === 'taken')
                                            <p class="text-sm font-bold text-emerald-600">{{ \Carbon\Carbon::parse($vaccine->updated_at)->format('M d, Y') }}</p>
                                        @else
                                            <p class="text-xs text-gray-300 font-bold tracking-tighter">PENDING</p>
                                        @endif
                                    </td>
                                    <td class="py-5 px-4 text-right">
                                        @if($vaccine->status !== 'taken')
                                            <form action="{{ route('patient.vaccinations.mark_taken', $vaccine->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-xs font-black text-teal-600 hover:text-teal-800 transition-colors bg-teal-50 px-4 py-2 rounded-xl border border-teal-100">
                                                    Mark Administered
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="text-xs font-black text-gray-300 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                                Verified ✓
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
