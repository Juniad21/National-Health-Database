@extends('layouts.doctor')

@section('header_title', 'Referral Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.referrals.index') }}" class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Referrals
        </a>
        <div class="px-4 py-2 bg-slate-100 rounded-xl">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Referral ID: #REF-{{ $referral->id }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-50">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Clinical Justification</h3>
                    <span class="px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest 
                        {{ $referral->priority === 'emergency' ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600' }}">
                        {{ $referral->priority }} Priority
                    </span>
                </div>
                
                <div class="space-y-6">
                    <div class="p-6 bg-slate-50 rounded-3xl">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Reason for Referral</p>
                        <p class="text-slate-700 leading-relaxed font-bold">{{ $referral->reason }}</p>
                    </div>

                    @if($referral->clinical_summary)
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-4">Clinical History Summary</p>
                            <div class="p-6 border border-slate-100 rounded-3xl italic text-slate-600 text-sm leading-relaxed">
                                {{ $referral->clinical_summary }}
                            </div>
                        </div>
                    @endif

                    @if($referral->recommended_tests)
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-4">Recommended Diagnostics</p>
                            <div class="p-6 border border-rose-100 bg-rose-50/20 rounded-3xl text-rose-800 font-bold text-sm">
                                {{ $referral->recommended_tests }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meta Info & Status -->
        <div class="space-y-8">
            <!-- Patient & Doctors -->
            <div class="bg-white rounded-[2rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Involved Parties</p>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-teal-600 font-black text-xs shadow-sm">P</div>
                        <div>
                            <p class="text-xs font-black text-slate-800">{{ $referral->patient->first_name }} {{ $referral->patient->last_name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Patient</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-blue-600 font-black text-xs shadow-sm">D1</div>
                        <div>
                            <p class="text-xs font-black text-slate-800">Dr. {{ $referral->referredByDoctor->first_name }} {{ $referral->referredByDoctor->last_name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Referring Specialist</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-teal-50 rounded-2xl border border-teal-100">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-teal-600 font-black text-xs shadow-sm">D2</div>
                        <div>
                            @if($referral->referredToDoctor)
                                <p class="text-xs font-black text-slate-800 uppercase">Dr. {{ $referral->referredToDoctor->first_name }} {{ $referral->referredToDoctor->last_name }}</p>
                            @elseif($referral->referredToHospital)
                                <p class="text-xs font-black text-slate-800 uppercase">{{ $referral->referredToHospital->name }}</p>
                            @else
                                <p class="text-xs font-black text-slate-800 uppercase">{{ $referral->department ?? 'General Dept' }}</p>
                            @endif
                            <p class="text-[9px] font-bold text-teal-600 uppercase tracking-widest">Referred To</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Actions -->
            <div class="bg-slate-900 rounded-[2rem] p-8 shadow-2xl shadow-slate-900/20 text-white">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Referral Status</p>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-3 h-3 rounded-full animate-pulse {{ $referral->status === 'pending' ? 'bg-blue-400' : ($referral->status === 'accepted' ? 'bg-emerald-400' : 'bg-slate-400') }}"></div>
                    <span class="text-lg font-black uppercase tracking-tight">{{ $referral->status }}</span>
                </div>

                @if(Auth::id() === $referral->referred_to_doctor_id || Auth::id() === $referral->referred_to_hospital_id)
                    <form action="{{ route('doctor.referrals.status', $referral) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Update Status</p>
                        <select name="status" class="w-full py-4 px-6 bg-slate-800 border-slate-700 rounded-2xl text-xs font-bold text-slate-300 focus:ring-teal-500/20">
                            <option value="pending" {{ $referral->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="accepted" {{ $referral->status === 'accepted' ? 'selected' : '' }}>Accept Referral</option>
                            <option value="completed" {{ $referral->status === 'completed' ? 'selected' : '' }}>Mark as Completed</option>
                            <option value="rejected" {{ $referral->status === 'rejected' ? 'selected' : '' }}>Reject Referral</option>
                        </select>
                        <button type="submit" class="w-full py-4 bg-teal-600 text-white font-black rounded-2xl hover:bg-teal-500 transition-all uppercase tracking-widest text-[10px]">Save Update</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
