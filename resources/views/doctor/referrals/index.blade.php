@extends('layouts.doctor')

@section('header_title', 'Clinical Referrals')

@section('content')
<div class="space-y-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Created by Me</p>
            <p class="text-3xl font-black text-slate-800">{{ $createdReferrals->count() }}</p>
            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="flex items-center justify-between p-3 bg-slate-800 rounded-xl">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wide">Sent (Pending)</span>
                    <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-400 rounded-lg text-xs font-black">{{ $referralStats['sent_pending'] }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Received by Me</p>
            <p class="text-3xl font-black text-teal-600">{{ $receivedReferrals->count() }}</p>
        </div>
    </div>

    <!-- Received Referrals -->
    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50">
        <h3 class="text-xl font-black text-slate-800 tracking-tight mb-8 flex items-center gap-3">
            Received Referrals
            <span class="px-3 py-1 bg-teal-50 rounded-lg text-[10px] font-black text-teal-600 uppercase tracking-widest">Incoming</span>
        </h3>

        @if($receivedReferrals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4 px-4">Patient</th>
                            <th class="pb-4 px-4">From Doctor</th>
                            <th class="pb-4 px-4">Status</th>
                            <th class="pb-4 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($receivedReferrals as $ref)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="py-5 px-4">
                                    <p class="font-black text-slate-800 text-sm">{{ $ref->patient->first_name }} {{ $ref->patient->last_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID: #{{ $ref->patient_id }}</p>
                                </td>
                                <td class="py-5 px-4 text-xs font-bold text-slate-500">Dr. {{ $ref->referredByDoctor->first_name }} {{ $ref->referredByDoctor->last_name }}</td>
                                <td class="py-5 px-4">
                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                                        {{ $ref->status === 'accepted' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($ref->status === 'pending' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-50 text-slate-600 border border-slate-100') }}">
                                        {{ $ref->status }}
                                    </span>
                                </td>
                                <td class="py-5 px-4 text-right">
                                    <a href="{{ route('doctor.referrals.show', $ref) }}" class="text-[9px] font-black text-teal-600 hover:text-white hover:bg-teal-600 border border-teal-100 px-4 py-2 rounded-xl transition-all uppercase tracking-widest">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No incoming referrals</p>
            </div>
        @endif
    </div>

    <!-- Created Referrals -->
    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50">
        <h3 class="text-xl font-black text-slate-800 tracking-tight mb-8 flex items-center gap-3">
            My Referrals
            <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest">Outgoing</span>
        </h3>

        @if($createdReferrals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4 px-4">Patient</th>
                            <th class="pb-4 px-4">Referred To</th>
                            <th class="pb-4 px-4">Status</th>
                            <th class="pb-4 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($createdReferrals as $ref)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="py-5 px-4">
                                    <p class="font-black text-slate-800 text-sm">{{ $ref->patient->first_name }} {{ $ref->patient->last_name }}</p>
                                </td>
                                <td class="py-5 px-4">
                                    @if($ref->referredToDoctor)
                                        <p class="text-xs font-bold text-slate-600 uppercase">Dr. {{ $ref->referredToDoctor->first_name }} {{ $ref->referredToDoctor->last_name }}</p>
                                    @elseif($ref->referredToHospital)
                                        <p class="text-xs font-bold text-slate-600 uppercase">{{ $ref->referredToHospital->name }}</p>
                                    @else
                                        <p class="text-xs font-bold text-slate-400 italic">{{ $ref->department ?? 'General' }}</p>
                                    @endif
                                </td>
                                <td class="py-5 px-4">
                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest">{{ $ref->status }}</span>
                                </td>
                                <td class="py-5 px-4 text-right">
                                    <a href="{{ route('doctor.referrals.show', $ref) }}" class="text-[9px] font-black text-slate-400 hover:text-slate-800 border border-slate-100 px-4 py-2 rounded-xl transition-all uppercase tracking-widest">Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">You haven't referred any patients yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
