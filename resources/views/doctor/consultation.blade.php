@extends('layouts.doctor')

@section('header_title', 'New Consultation - ' . $patient->first_name . ' ' . $patient->last_name)

@section('content')
    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Patient Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-indigo-500 hover:shadow-md transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                            <div class="flex flex-wrap items-center gap-4 text-gray-600 mt-1">
                                <span class="bg-gray-100 flex items-center px-3 py-1 rounded-md">
                                    <span class="font-semibold text-gray-700 mr-2">Age:</span> {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs
                                </span>
                                <span class="bg-red-50 flex items-center px-3 py-1 rounded-md border border-red-100">
                                    <span class="font-semibold text-gray-700 mr-2">Blood Group:</span> <span class="text-red-600 font-bold">{{ $patient->blood_group }}</span>
                                </span>
                                <span class="bg-gray-100 flex items-center px-3 py-1 rounded-md">
                                    <span class="font-semibold text-gray-700 mr-2">Gender:</span> {{ ucfirst($patient->gender) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex gap-4 items-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 shadow-sm animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                In Progress
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('doctor.consultation.store', $patient->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Notes & Lab -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Clinical Notes -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center border-b pb-3 border-gray-100">
                                    <svg class="w-6 h-6 mr-2 text-indigo-500 bg-indigo-50 rounded p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Clinical Diagnosis
                                </h3>
                                <div class="space-y-5">
                                    <div>
                                        <label for="diagnosis" class="block text-sm font-semibold text-gray-700 mb-1">Primary Diagnosis <span class="text-red-500">*</span></label>
                                        <input type="text" name="diagnosis" id="diagnosis" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors bg-gray-50 focus:bg-white" placeholder="e.g., Viral Pharyngitis">
                                    </div>
                                    <div>
                                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Visit Notes</label>
                                        <textarea name="notes" id="notes" rows="6" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors bg-gray-50 focus:bg-white" placeholder="Patient presented with...&#10;Symptoms started 3 days ago..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lab Tests -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition-shadow" x-data="{ 
                            search: '', 
                            selected: [], 
                            tests: {{ json_encode($labTests->map(fn($t) => ['id' => $t->id, 'name' => $t->test_name])) }} 
                        }">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center border-b pb-3 border-gray-100">
                                    <svg class="w-6 h-6 mr-2 text-blue-500 bg-blue-50 rounded p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    Order Lab Tests
                                </h3>

                                {{-- Hospital notice for lab tests --}}
                                @php $doctorHospital = Auth::user()->doctor->hospital; @endphp
                                @if($doctorHospital)
                                    <div class="mb-4 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-bold text-blue-800">Lab tests go to your hospital:</p>
                                            <p class="text-sm text-blue-700 font-semibold">🏥 {{ $doctorHospital->name }}</p>
                                            <p class="text-xs text-blue-500 mt-0.5">The patient must attend this hospital to complete any ordered tests.</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="relative space-y-4">
                                    <select x-on:change="
                                        let selectedId = parseInt($event.target.value);
                                        let test = tests.find(t => t.id === selectedId);
                                        if(test && !selected.find(t => t.id === test.id)) {
                                            selected.push(test);
                                        }
                                        $event.target.value = '';
                                    " class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 focus:bg-white text-sm font-medium">
                                        <option value="">+ Assign a new Test...</option>
                                        @foreach($labTests as $test)
                                            <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Selected Pills -->
                                    <div class="flex flex-col space-y-2 mt-4" x-show="selected.length > 0" x-cloak>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned Tests:</p>
                                        <template x-for="s in selected" :key="s.id">
                                            <div class="flex justify-between items-center bg-white border border-blue-200 shadow-sm text-blue-800 text-sm px-4 py-2 rounded-lg group hover:border-blue-300 transition-colors">
                                                <div class="flex items-center">
                                                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-3"></span>
                                                    <span x-text="s.name" class="font-medium"></span>
                                                    <input type="hidden" name="lab_test_ids[]" :value="s.id">
                                                </div>
                                                <button type="button" @click="selected = selected.filter(t => t.id !== s.id)" class="text-gray-400 hover:text-red-500 p-1 rounded-md hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <div x-show="selected.length === 0" class="text-center p-4 border border-dashed border-gray-200 rounded-lg bg-gray-50">
                                        <span class="text-sm text-gray-400">No lab tests assigned yet.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Prescription Builder -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition-shadow" x-data="{ 
                            meds: [{ id: Date.now(), name: '', dosage: '', duration: '', instructions: '' }],
                            addMed() {
                                this.meds.push({ id: Date.now(), name: '', dosage: '', duration: '', instructions: '' });
                            },
                            removeMed(id) {
                                this.meds = this.meds.filter(m => m.id !== id);
                            }
                        }">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 border-gray-100">
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                        <svg class="w-7 h-7 mr-3 text-emerald-500 bg-emerald-50 rounded-lg p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg> 
                                        E-Prescription Builder
                                    </h3>
                                    <button type="button" @click="addMed()" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-emerald-200 text-sm font-semibold rounded-lg shadow-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Add Medicine
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <template x-for="(med, index) in meds" :key="med.id">
                                        <div class="border border-gray-200 rounded-xl p-5 bg-white shadow-sm relative group hover:border-emerald-300 transition-colors">
                                            <!-- Remove button -->
                                            <button type="button" @click="removeMed(med.id)" class="absolute -top-3 -right-3 bg-white text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full p-1.5 shadow-sm border border-gray-100 transition-all z-10 opacity-0 group-hover:opacity-100 focus:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                <div class="md:col-span-2 lg:col-span-1">
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Medicine Name <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="med.name" :name="'medications['+index+'][name]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-gray-50 focus:bg-white text-sm" placeholder="e.g., Paracetamol 500mg">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dosage <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="med.dosage" :name="'medications['+index+'][dosage]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-gray-50 focus:bg-white text-sm" placeholder="e.g., 1-1-1">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Duration <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="med.duration" :name="'medications['+index+'][duration]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-gray-50 focus:bg-white text-sm" placeholder="e.g., 5 Days">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Instructions <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="med.instructions" :name="'medications['+index+'][instructions]'" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 bg-gray-50 focus:bg-white text-sm" placeholder="e.g., After meal">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="meds.length === 0">
                                        <div class="text-center py-10 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                                            <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="text-gray-500 font-medium">No medicines added.</p>
                                            <button type="button" @click="addMed()" class="mt-3 text-emerald-600 hover:text-emerald-700 font-semibold text-sm">Click to add</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="mt-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center z-50 transition-colors">
                    <p class="text-sm border-2 border-transparent text-gray-500 hidden md:block">
                        <span class="font-bold">Note:</span> Completing the consultation will remove the patient from your queue.
                    </p>
                    <div class="flex w-full sm:w-auto items-center">
                        <a href="{{ route('doctor.dashboard') }}" class="w-full sm:w-auto text-center bg-white py-3 px-5 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="w-full sm:w-auto flex justify-center items-center py-3 px-8 border border-transparent shadow-md text-base font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Complete & Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection