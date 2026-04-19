@extends('layouts.doctor')

@section('header_title', 'Patient Profile')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('doctor.dashboard') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Dashboard
            </a>
            <a href="{{ route('doctor.consultation', $patient->id) }}"
                class="px-5 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Consultation
            </a>
        </div>

        <!-- Patient Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-start gap-6">
            <div
                class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-2xl font-bold border-4 border-white shadow-md">
                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                    <span
                        class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200">{{ $patient->blood_group }}</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4">
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">NID</span><span
                            class="font-medium">{{ $patient->nid }}</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Age/DOB</span><span
                            class="font-medium">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs
                            ({{ $patient->date_of_birth }})</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Gender</span><span
                            class="font-medium capitalize">{{ $patient->gender }}</span></div>
                    <div><span class="block text-gray-500 text-xs uppercase tracking-wider">Contact</span><span
                            class="font-medium">{{ $patient->phone }}</span></div>
                </div>
            </div>
        </div>

        <!-- Add Medical Record Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Add Medical Record</h3>
                <p class="text-sm text-gray-500 mt-1">Add prescriptions, lab tests, or guidelines for this patient</p>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200" role="tablist">
                <button class="tab-button active px-6 py-4 font-medium text-gray-700 border-b-2 border-blue-500 hover:text-blue-600 transition-colors" data-tab="prescription">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Prescription
                </button>
                <button class="tab-button px-6 py-4 font-medium text-gray-700 hover:text-blue-600 transition-colors" data-tab="lab_test">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Lab Test
                </button>
                <button class="tab-button px-6 py-4 font-medium text-gray-700 hover:text-blue-600 transition-colors" data-tab="document">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Guidelines
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <ul class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Prescription Tab -->
                <div id="prescription" class="tab-content">
                    <form action="{{ route('doctor.medical_record.store', $patient->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="prescription">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prescription Title</label>
                            <input type="text" name="title" placeholder="e.g., Pain Relief" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medicines & Dosages</label>
                            <textarea name="notes" rows="5" placeholder="Enter medicines with dosages&#10;e.g., Aspirin 500mg - 2 tablets twice daily&#10;Ibuprofen 200mg - 1 tablet thrice daily" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Save Prescription
                        </button>
                    </form>
                </div>

                <!-- Lab Test Tab -->
                <div id="lab_test" class="tab-content hidden">
                    <form action="{{ route('doctor.medical_record.store', $patient->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="lab_test">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lab Test Name</label>
                            <input type="text" name="title" placeholder="e.g., Complete Blood Count (CBC)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Test Details/Instructions</label>
                            <textarea name="notes" rows="4" placeholder="Enter test requirements or special instructions" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <span class="font-semibold">📍 Hospital:</span> This test will be sent to <span class="font-bold text-blue-600">{{ Auth::user()->doctor->hospital->name ?? 'N/A' }}</span>
                            </p>
                        </div>

                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <span class="font-semibold">⏳ Status:</span> Lab tests will be marked as <span class="font-bold text-yellow-600">Pending 🟡</span> until {{ Auth::user()->doctor->hospital->name ?? 'the hospital' }} completes and uploads the results.
                            </p>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            Request Lab Test
                        </button>
                    </form>
                </div>

                <!-- Document/Guidelines Tab -->
                <div id="document" class="tab-content hidden">
                    <form action="{{ route('doctor.medical_record.store', $patient->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="document">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guidelines Title</label>
                            <input type="text" name="title" placeholder="e.g., Post-Surgery Care Instructions" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Guidelines & Advice</label>
                            <textarea name="notes" rows="5" placeholder="Enter doctor's advice and guidelines for the patient" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 transition-colors">
                            Add Guidelines
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Medical History Timeline -->
        <h3 class="font-bold text-gray-800 text-lg mt-8 mb-4">Medical History</h3>

        @if($records->isEmpty())
            <div class="text-center py-12 bg-white rounded-2xl border border-gray-200 border-dashed">
                <p class="text-gray-500">No medical history available for this patient.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($records as $type => $group)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2 bg-gray-50/50">
                            @if($type === 'prescription')
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">Prescriptions & Consultations</h4>
                            @elseif($type === 'lab_test')
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">Laboratory Tests</h4>
                            @elseif($type === 'document')
                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">Guidelines & Documents</h4>
                            @else
                                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                <h4 class="font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $type) }}</h4>
                            @endif
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($group as $record)
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h5 class="font-bold text-gray-800">{{ $record->diagnosis }}</h5>
                                            @if($type === 'lab_test')
                                                <div class="mt-2 flex items-center gap-2">
                                                    @if($record->status === 'pending')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold border border-yellow-300">
                                                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                                            Pending 🟡
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold border border-green-300">
                                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                            Completed ✓
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <span
                                            class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</span>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 mt-3 border border-gray-100 whitespace-pre-line">
                                        {!! nl2br(e($record->medications_or_results)) !!}
                                    </div>
                                    <p class="text-xs text-gray-400 mt-3 font-medium">Recorded by: Dr. {{ $record->doctor->first_name }}
                                        {{ $record->doctor->last_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.add('hidden');
                });
                
                // Remove active styling from all buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('text-gray-700');
                });
                
                // Show selected tab
                document.getElementById(tabName).classList.remove('hidden');
                
                // Add active styling to clicked button
                this.classList.remove('text-gray-700');
                this.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            });
        });
    </script>
@endsection