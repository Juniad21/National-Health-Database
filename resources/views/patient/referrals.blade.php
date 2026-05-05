@extends('layouts.patient')

@section('header_title', 'My Clinical Referrals')

@section('content')
<div class="space-y-8">
    <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-50">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight mb-2">Referral History</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Track your specialist transitions and medical transfers</p>
            </div>
            <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
        </div>

        @if($referrals->count() > 0)
            <div class="space-y-6">
                @foreach($referrals as $ref)
                    <div class="group bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:bg-white hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 relative overflow-hidden">
                        {{-- Status Ribbon --}}
                        <div class="absolute top-0 right-0 px-6 py-2 rounded-bl-3xl text-[10px] font-black uppercase tracking-widest
                            {{ $ref->status === 'accepted' ? 'bg-emerald-500 text-white' : ($ref->status === 'pending' ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                            {{ $ref->status }}
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                            {{-- From Specialist --}}
                            <div class="space-y-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Referring Specialist</p>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 font-black">DR</div>
                                    <div>
                                        <p class="font-black text-slate-800 text-base">Dr. {{ $ref->referredByDoctor->first_name }} {{ $ref->referredByDoctor->last_name }}</p>
                                        <p class="text-xs font-bold text-slate-400">{{ $ref->referredByDoctor->specialty ?? 'General Physician' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Transition --}}
                            <div class="flex flex-col items-center justify-center space-y-2 opacity-30 group-hover:opacity-100 transition-opacity">
                                <div class="h-px w-full bg-slate-200 hidden lg:block"></div>
                                <svg class="w-6 h-6 text-slate-400 transform rotate-90 lg:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>

                            {{-- To Specialist --}}
                            <div class="space-y-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Destination Care</p>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-teal-600 rounded-2xl shadow-lg flex items-center justify-center text-white font-black">TO</div>
                                    <div>
                                        @if($ref->referredToDoctor)
                                            <p class="font-black text-slate-800 text-base">Dr. {{ $ref->referredToDoctor->first_name }} {{ $ref->referredToDoctor->last_name }}</p>
                                            <p class="text-xs font-bold text-teal-600 uppercase">{{ $ref->referredToDoctor->hospital->name ?? 'Specialist Clinic' }}</p>
                                        @elseif($ref->referredToHospital)
                                            <p class="font-black text-slate-800 text-base">{{ $ref->referredToHospital->name }}</p>
                                            <p class="text-xs font-bold text-teal-600 uppercase">{{ $ref->department ?? 'General Dept' }}</p>
                                        @else
                                            <p class="font-black text-slate-800 text-base">{{ $ref->department ?? 'Specialist Center' }}</p>
                                            <p class="text-xs font-bold text-slate-400 uppercase">Consultation Registry</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reason/Note --}}
                        <div class="mt-8 pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Reason for Referral</p>
                                <p class="text-sm font-bold text-slate-600 leading-relaxed bg-white/50 p-4 rounded-2xl border border-slate-100">{{ $ref->reason }}</p>
                            </div>
                            @if($ref->status === 'accepted')
                                <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 flex items-start gap-4">
                                    <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-emerald-700 uppercase tracking-tight mb-1">Referral Accepted</p>
                                        <p class="text-[10px] font-bold text-emerald-600 leading-snug">The destination specialist has reviewed your case. You may now contact their department to schedule your visit.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-6 flex justify-between items-center text-[9px] font-black text-slate-300 uppercase tracking-widest">
                            <span>Issued: {{ \Carbon\Carbon::parse($ref->created_at)->format('d M, Y') }}</span>
                            <span>Ref ID: #REF-{{ $ref->id }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
                <div class="w-20 h-20 bg-white rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-6 text-slate-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h4 class="text-xl font-black text-slate-800 tracking-tight mb-2">No Referrals Found</h4>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">When a doctor refers you to a specialist, it will appear here</p>
            </div>
        @endif
    </div>
</div>
@endsection
