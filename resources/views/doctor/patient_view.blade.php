@extends('layouts.doctor')

@section('header_title', 'Patient Profile & Consultation')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('doctor.dashboard') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                    Patient Profile
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-2xl text-green-800 font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Patient Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-2xl font-black border-4 border-white shadow-md flex-shrink-0">
                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-black text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">{{ $patient->blood_group }}</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-2 text-sm mt-3">
                    <div><span class="block text-gray-400 text-[10px] uppercase font-bold tracking-widest">NID</span><span class="font-bold text-gray-700">{{ $patient->nid }}</span></div>
                    <div><span class="block text-gray-400 text-[10px] uppercase font-bold tracking-widest">Age</span><span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Years</span></div>
                    <div><span class="block text-gray-400 text-[10px] uppercase font-bold tracking-widest">Gender</span><span class="font-bold text-gray-700 capitalize">{{ $patient->gender }}</span></div>
                    <div><span class="block text-gray-400 text-[10px] uppercase font-bold tracking-widest">Contact</span><span class="font-bold text-gray-700">{{ $patient->phone }}</span></div>
                </div>
            </div>
        </div>

        <!-- Consultation & Record Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative" x-data="{ activeTab: 'live_consultation' }">
            {{-- FIXED BY JUNAID: Security Layer - Access Control Overlay --}}
            @if(!$hasConsent)
                <div class="absolute inset-0 z-50 bg-white/60 backdrop-blur-md flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-6 shadow-xl shadow-rose-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 mb-2">Patient Consent Required</h3>
                    <p class="text-gray-500 max-w-md mb-8 font-medium">To protect patient privacy, you must request and receive explicit consent before viewing medical history or starting a consultation.</p>
                    
                    @if($pendingRequest)
                        <button disabled class="bg-gray-100 text-gray-500 px-8 py-4 rounded-2xl font-black flex items-center gap-3 cursor-not-allowed">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Waiting for Approval...
                        </button>
                    @else
                        <form action="{{ route('doctor.patient.request_access', $patient->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-rose-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-rose-200 hover:bg-rose-700 transition-all transform hover:-translate-y-1">
                                Request Access Now
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
                <h3 class="font-black text-gray-800 text-lg">Medical Actions</h3>
                <div class="flex bg-gray-200/50 p-1 rounded-xl">
                    <button @click="activeTab = 'live_consultation'" :class="activeTab === 'live_consultation' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all">
                        Live Consultation
                    </button>
                    <button @click="activeTab = 'guidelines'" :class="activeTab === 'guidelines' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all ml-1">
                        General Guidelines
                    </button>
                    <button @click="activeTab = 'vaccination'" :class="activeTab === 'vaccination' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all ml-1">
                        Prescribe Vaccine
                    </button>
                    <a href="{{ route('doctor.referrals.create', $patient->id) }}" class="px-4 py-1.5 rounded-lg text-sm font-bold transition-all ml-1 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Refer Patient
                    </a>
                </div>
            </div>

            <div class="p-6">
                <!-- LIVE CONSULTATION TAB -->
                <div x-show="activeTab === 'live_consultation'">
                    <form action="{{ route('doctor.consultation.store', $patient->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left: Diagnosis & Lab -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Primary Diagnosis <span class="text-red-500">*</span></label>
                                    <input type="text" name="diagnosis" required placeholder="e.g. Chronic Hypertension" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                                </div>
                                
                                <div x-data="{ 
                                    search: '', 
                                    selected: [], 
                                    tests: {{ json_encode($labTests->map(fn($t) => ['id' => $t->id, 'name' => $t->test_name])) }} 
                                }">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Assign Lab Tests</label>
                                    <select x-on:change="
                                        let selectedId = parseInt($event.target.value);
                                        let test = tests.find(t => t.id === selectedId);
                                        if(test && !selected.find(t => t.id === test.id)) {
                                            selected.push(test);
                                        }
                                        $event.target.value = '';
                                    " class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-sm">
                                        <option value="">+ Add a Lab Test...</option>
                                        @foreach($labTests as $test)
                                            <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                                        @endforeach
                                    </select>

                                    <div class="mt-3 flex flex-wrap gap-2" x-show="selected.length > 0">
                                        <template x-for="s in selected" :key="s.id">
                                            <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100 text-xs font-bold">
                                                <span x-text="s.name"></span>
                                                <input type="hidden" name="lab_test_ids[]" :value="s.id">
                                                <button type="button" @click="selected = selected.filter(t => t.id !== s.id)" class="text-blue-400 hover:text-red-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2 italic">Tests will be conducted at: {{ Auth::user()->doctor->hospital->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Right: Medication Builder -->
                            <div x-data="{ 
                                meds: [{ id: Date.now(), name: '', dosage: '', duration: '', instructions: '' }],
                                addMed() { this.meds.push({ id: Date.now(), name: '', dosage: '', duration: '', instructions: '' }); },
                                removeMed(id) { this.meds = this.meds.filter(m => m.id !== id); }
                            }">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="text-sm font-bold text-gray-700">Prescription Builder</label>
                                    <button type="button" @click="addMed()" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Add Medicine
                                    </button>
                                </div>

                                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                    <template x-for="(med, index) in meds" :key="med.id">
                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl relative group">
                                            <button type="button" @click="removeMed(med.id)" class="absolute -top-2 -right-2 bg-red-100 text-red-600 p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <div class="grid grid-cols-1 gap-3">
                                                <input type="text" x-model="med.name" :name="'medications['+index+'][name]'" required placeholder="Medicine Name" class="w-full text-xs rounded-lg border-gray-200">
                                                <div class="grid grid-cols-3 gap-2">
                                                    <input type="text" x-model="med.dosage" :name="'medications['+index+'][dosage]'" required placeholder="Dosage (1-0-1)" class="text-[10px] rounded-lg border-gray-200">
                                                    <input type="text" x-model="med.duration" :name="'medications['+index+'][duration]'" required placeholder="Duration (7 Days)" class="text-[10px] rounded-lg border-gray-200">
                                                    <input type="text" x-model="med.instructions" :name="'medications['+index+'][instructions]'" required placeholder="Instructions" class="text-[10px] rounded-lg border-gray-200">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all transform hover:-translate-y-0.5">
                                ✓ Save & Finish Consultation
                            </button>
                        </div>
                    </form>
                </div>

                <!-- GUIDELINES TAB -->
                <div x-show="activeTab === 'guidelines'" x-cloak>
                    <form action="{{ route('doctor.medical_record.store', $patient->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="document">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
                            <input type="text" name="title" required placeholder="e.g. Post-Surgery Care" class="w-full rounded-xl border-gray-200 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Instructions</label>
                            <textarea name="notes" rows="4" required placeholder="Enter detailed guidelines..." class="w-full rounded-xl border-gray-200 bg-gray-50"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-teal-700 transition-colors">
                                Add Guidelines
                            </button>
                        </div>
                    </form>
                </div>

                <!-- VACCINATION TAB -->
                <div x-show="activeTab === 'vaccination'" x-cloak>
                    <form action="{{ route('doctor.prescribe_vaccine', $patient->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Vaccine Name</label>
                                <input type="text" name="vaccine_name" required placeholder="e.g. Covid-19 (Pfizer)" class="w-full rounded-xl border-gray-200 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Dose Number</label>
                                <input type="number" name="dose_number" required value="1" min="1" class="w-full rounded-xl border-gray-200 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Scheduled Date</label>
                                <input type="date" name="scheduled_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-200 bg-gray-50">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all transform hover:-translate-y-0.5">
                                ✓ Prescribe Vaccination
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Medical History Timeline -->
        <div class="relative">
            {{-- FIXED BY JUNAID: Blur history if no consent --}}
            @if(!$hasConsent)
                <div class="absolute inset-0 z-10 bg-white/40 backdrop-blur-[2px] rounded-3xl"></div>
            @endif
            
            <h3 class="font-black text-gray-800 text-lg mt-10 mb-4">Patient Medical History</h3>

            @if($records->isEmpty())
                <div class="text-center py-12 bg-white rounded-3xl border border-gray-200 border-dashed">
                    <p class="text-gray-400 font-medium">No prior records found for this patient.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($records as $type => $group)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/20">
                                <h4 class="font-black text-gray-700 uppercase tracking-widest text-xs">
                                    {{ $type === 'prescription' ? 'Prescriptions' : ($type === 'lab' ? 'Lab Reports' : str_replace('_', ' ', $type)) }}
                                </h4>
                            </div>
                            <div class="divide-y divide-gray-50">
                                @foreach($group as $record)
                                    <div class="p-6 hover:bg-gray-50/50 transition-colors">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h5 class="font-bold text-gray-800 text-lg">{{ $record->diagnosis }}</h5>
                                                <p class="text-xs text-gray-400 font-medium mt-1">Recorded by Dr. {{ $record->doctor->first_name }} {{ $record->doctor->last_name }}</p>
                                            </div>
                                            <span class="text-[10px] font-black text-gray-400 bg-gray-100 px-2 py-1 rounded-md">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-600 border border-gray-100 whitespace-pre-line font-medium leading-relaxed">
                                            {!! nl2br(e($record->medications_or_results)) !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
@endsection