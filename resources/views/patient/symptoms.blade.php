@extends('layouts.patient')

@section('header_title', 'Smart Symptom Assessment')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{
    step: 1,
    selectedSymptoms: [],
    severity: 'moderate',
    duration: '1-3 days',
    notes: '',
    isSubmitting: false,
    assessmentResult: null,
    history: {{ $history->toJson() }},
    
    toggleSymptom(symptom) {
        if (this.selectedSymptoms.includes(symptom)) {
            this.selectedSymptoms = this.selectedSymptoms.filter(s => s !== symptom);
        } else {
            this.selectedSymptoms.push(symptom);
        }
    },
    
    async submitAssessment() {
        this.isSubmitting = true;
        try {
            const response = await fetch('{{ route('patient.symptoms') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    symptoms: this.selectedSymptoms,
                    severity: this.severity,
                    duration: this.duration,
                    notes: this.notes
                })
            });
            
            const data = await response.json();
            if (data.success) {
                this.assessmentResult = data.assessment;
                this.history.unshift(data.assessment);
                this.step = 3;
            }
        } catch (error) {
            console.error('Submission failed', error);
        } finally {
            this.isSubmitting = false;
        }
    },
    
    resetWizard() {
        this.step = 1;
        this.selectedSymptoms = [];
        this.severity = 'moderate';
        this.duration = '1-3 days';
        this.notes = '';
        this.assessmentResult = null;
    }
}">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Wizard Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Welcome Step -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-[2rem] shadow-xl shadow-emerald-900/5 border border-emerald-50 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
                <div class="relative p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-slate-800 leading-tight">Intelligent Diagnosis</h2>
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Step 1: Symptom Mapping</p>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-slate-700 mb-6 uppercase tracking-tight">Select all that apply:</h3>
                    
                    <div class="space-y-8">
                        @foreach($symptomsList as $category => $symptoms)
                            <div class="space-y-3">
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1">{{ $category }} Indicators</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($symptoms as $symptom)
                                        <button 
                                            @click="toggleSymptom('{{ $symptom }}')"
                                            :class="selectedSymptoms.includes('{{ $symptom }}') ? 'bg-emerald-600 text-white border-emerald-600 shadow-md scale-105' : 'bg-slate-50 text-slate-600 border-slate-100 hover:border-emerald-200'"
                                            class="px-4 py-2 rounded-xl border text-xs font-black transition-all duration-200 flex items-center gap-2">
                                            <span x-show="selectedSymptoms.includes('{{ $symptom }}')" x-transition class="w-2 h-2 bg-white rounded-full"></span>
                                            {{ $symptom }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-50 flex justify-between items-center">
                        <p class="text-xs font-bold text-slate-400">Selected: <span class="text-emerald-600 font-black" x-text="selectedSymptoms.length"></span> items</p>
                        <button 
                            @click="step = 2" 
                            :disabled="selectedSymptoms.length === 0"
                            class="px-8 py-4 bg-slate-800 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 disabled:opacity-30 disabled:hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">
                            Continue to Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Details Step -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-indigo-50 overflow-hidden relative" style="display: none;">
                <div class="relative p-10">
                    <div class="flex items-center gap-4 mb-10">
                        <button @click="step = 1" class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <div>
                            <h2 class="text-3xl font-black text-slate-800 leading-tight">Severity Analysis</h2>
                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Step 2: Contextual Data</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pain/Severity Scale</label>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="lvl in ['mild', 'moderate', 'severe']">
                                    <button 
                                        @click="severity = lvl"
                                        :class="severity === lvl ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg' : 'bg-slate-50 text-slate-500 border-slate-100'"
                                        class="py-4 rounded-2xl border text-[10px] font-black uppercase tracking-widest transition-all capitalize" x-text="lvl">
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Symptoms Duration</label>
                            <select x-model="duration" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-black text-slate-700 focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Less than 24h</option>
                                <option>1-3 days</option>
                                <option>4-7 days</option>
                                <option>1-2 weeks</option>
                                <option>Chronic (2+ weeks)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4 mb-10">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Additional Observations</label>
                        <textarea x-model="notes" rows="4" class="w-full bg-slate-50 border-slate-100 rounded-3xl py-4 px-6 text-sm font-black text-slate-700 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe how the symptoms started or any other relevant info..."></textarea>
                    </div>

                    <button 
                        @click="submitAssessment()" 
                        :disabled="isSubmitting"
                        class="w-full py-6 bg-slate-900 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-2xl shadow-slate-300 flex items-center justify-center gap-3">
                        <span x-show="!isSubmitting">Generate Diagnostic Report</span>
                        <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-show="isSubmitting">Analyzing Clinical Data...</span>
                    </button>
                </div>
            </div>

            <!-- Result Step -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-[2rem] shadow-2xl shadow-emerald-900/10 border border-emerald-50 overflow-hidden relative" style="display: none;">
                <div class="bg-emerald-600 p-10 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                    <div class="relative flex items-center gap-6">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/30">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-4xl font-black leading-tight">Clinical Insight</h2>
                            <p class="text-emerald-100 font-black uppercase tracking-widest text-[10px] opacity-80">Assessment ID: #<span x-text="assessmentResult?.id"></span></p>
                        </div>
                    </div>
                </div>

                <div class="p-10">
                    <div class="max-w-2xl mx-auto space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Recommended Specialty</p>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center font-black">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-800" x-text="assessmentResult?.suggested_specialty"></h3>
                                </div>
                            </div>

                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Confidence Score</p>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 h-3 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" :style="'width: ' + assessmentResult?.analysis_results.confidence + '%'"></div>
                                    </div>
                                    <span class="text-xl font-black text-slate-800" x-text="assessmentResult?.analysis_results.confidence + '%'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button @click="const url = '{{ route('patient.scheduling') }}?specialty=' + encodeURIComponent(assessmentResult?.suggested_specialty); window.location.href = url;" 
                               class="w-full py-6 bg-emerald-500 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-emerald-400 shadow-2xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
                                BOOK CONSULTATION
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-center">
                        <button @click="resetWizard()" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Start New Assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / History -->
        <div class="space-y-6">
            <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-slate-900/20">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full -mr-16 -mt-16"></div>
                <h3 class="text-xl font-black mb-6 uppercase tracking-tight relative z-10">Recent History</h3>
                
                <div class="space-y-4 relative z-10">
                    <template x-for="item in history" :key="item.id">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-all group cursor-pointer">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest" x-text="new Date(item.created_at).toLocaleDateString()"></span>
                                <span class="px-2 py-0.5 bg-white/10 rounded text-[8px] font-black uppercase" x-text="item.severity"></span>
                            </div>
                            <h4 class="text-sm font-black text-white group-hover:text-emerald-300 transition-colors" x-text="item.suggested_specialty"></h4>
                            <p class="text-[10px] text-slate-400 font-bold mt-1 line-clamp-1" x-text="item.selected_symptoms.join(', ')"></p>
                        </div>
                    </template>
                    
                    <template x-if="history.length === 0">
                        <div class="py-10 text-center opacity-40">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-[10px] font-black uppercase tracking-widest">No prior clinical data</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="bg-amber-50 rounded-3xl p-6 border border-amber-100">
                <div class="flex items-center gap-3 mb-3 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-xs font-black uppercase tracking-widest">Medical Disclaimer</span>
                </div>
                <p class="text-[10px] text-amber-800 font-bold leading-relaxed">This tool provides informational clinical insights and is NOT a substitute for professional medical advice. In case of severe chest pain or breathing difficulty, trigger a <a href="{{ route('patient.emergency.sos') }}" class="text-red-600 underline">SOS alert</a> immediately.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>
@endsection