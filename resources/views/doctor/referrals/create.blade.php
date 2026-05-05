@extends('layouts.doctor')

@section('header_title', 'Create Referral')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-[2.5rem] p-10 shadow-xl shadow-slate-200/50 border border-slate-50">
        <div class="flex items-center gap-6 mb-10 pb-10 border-b border-slate-50">
            <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600 font-black text-2xl shadow-inner">
                {{ substr($patientUser->first_name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-none mb-2">Refer {{ $patientUser->first_name }} {{ $patientUser->last_name }}</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Digital Health ID: #{{ $patientUser->id }}</p>
            </div>
        </div>

        <form action="{{ route('doctor.referrals.store', $patientUser->id) }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Referral Type --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Referral Type</label>
                    <select name="referral_type" required class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                        <option value="specialist">Specialist Consultation</option>
                        <option value="hospital">Hospital Transfer</option>
                        <option value="department">Inter-Departmental</option>
                        <option value="diagnostic">Diagnostic/Pathology</option>
                        <option value="emergency">Emergency Stabilization</option>
                    </select>
                    @error('referral_type') <p class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</p> @enderror
                </div>

                {{-- Priority --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Priority Level</label>
                    <select name="priority" required class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                        <option value="normal">Normal / Routine</option>
                        <option value="urgent">Urgent / High Priority</option>
                        <option value="emergency">Life-Threatening / Emergency</option>
                    </select>
                    @error('priority') <p class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- To Doctor --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Refer to Specialist (Optional)</label>
                    <select name="referred_to_doctor_id" class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                        <option value="">Search National Registry...</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->user_id }}">Dr. {{ $doc->first_name }} {{ $doc->last_name }} ({{ $doc->specialty ?? 'General' }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- To Hospital --}}
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Refer to Facility (Optional)</label>
                    <select name="referred_to_hospital_id" class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                        <option value="">Select Facility...</option>
                        @foreach($hospitals as $hosp)
                            <option value="{{ $hosp->user_id }}">{{ $hosp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Department --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Target Department / Lab</label>
                <input type="text" name="department" placeholder="e.g. Cardiology, Hematology Lab..." class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
            </div>

            {{-- Reason --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Reason for Referral (Required)</label>
                <textarea name="reason" rows="4" required placeholder="Detailed clinical justification..." class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all"></textarea>
                @error('reason') <p class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</p> @enderror
            </div>

            {{-- Clinical Summary --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Clinical History Summary</label>
                <textarea name="clinical_summary" rows="4" placeholder="Brief summary of patient's medical history..." class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all"></textarea>
            </div>

            {{-- Recommended Tests --}}
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Recommended Diagnostic Tests</label>
                <textarea name="recommended_tests" rows="3" placeholder="List any specific tests you want performed..." class="w-full py-4 px-6 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all"></textarea>
            </div>

            <div class="flex gap-4 pt-6">
                <a href="{{ route('doctor.patient.view', $patientUser->id) }}" class="flex-1 py-5 bg-slate-100 text-slate-500 font-black rounded-3xl hover:bg-slate-200 transition-all text-center uppercase tracking-widest text-xs">Cancel</a>
                <button type="submit" class="flex-1 py-5 bg-teal-600 text-white font-black rounded-3xl hover:bg-teal-700 shadow-xl shadow-teal-100 transition-all uppercase tracking-widest text-xs">Create Referral</button>
            </div>
        </form>
    </div>
</div>
@endsection
