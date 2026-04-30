@extends('layouts.doctor')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Emergency Triage: #{{ $emergency->id }}</h1>
            <span class="bg-red-100 text-red-600 px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                Critical Case
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                <!-- Patient Profile -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Patient Profile</h3>
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                            {{ substr($emergency->patient->first_name, 0, 1) }}{{ substr($emergency->patient->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $emergency->patient->first_name }} {{ $emergency->patient->last_name }}</h2>
                            <p class="text-sm text-gray-500">Blood Group: <span class="font-bold text-red-600">{{ $emergency->patient->blood_group }}</span> | Age: {{ \Carbon\Carbon::parse($emergency->patient->date_of_birth)->age }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Emergency Type</p>
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->emergency_type }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Contact</p>
                            <p class="text-sm font-bold text-gray-800">{{ $emergency->contact_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Triage Form -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-indigo-100">
                    <h3 class="font-bold text-gray-800 mb-4">Doctor's Triage Notes</h3>
                    <form action="{{ route('doctor.emergency.triage', $emergency->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Initial Assessment & Findings</label>
                            <textarea name="notes" rows="6" required class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter patient condition, vital signs, and immediate actions taken..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adjust Severity</label>
                            <select name="severity" required class="w-full rounded-xl border-gray-200">
                                <option value="low" {{ $emergency->severity === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $emergency->severity === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $emergency->severity === 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ $emergency->severity === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-100 transition-all">
                            Save Triage Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Symptoms Sidebar -->
                <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                    <h3 class="text-sm font-black text-red-600 uppercase mb-4 tracking-wider">Reported Symptoms</h3>
                    <p class="text-sm text-red-800 italic leading-relaxed">
                        "{{ $emergency->symptoms ?: 'No symptoms reported by patient.' }}"
                    </p>
                </div>

                <!-- Medical History -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Quick History</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Allergies</span>
                            <span class="font-bold text-red-500">Penicillin</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Chronic</span>
                            <span class="font-bold text-gray-800">Diabetes</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Last Visit</span>
                            <span class="font-bold text-gray-800">2 months ago</span>
                        </div>
                    </div>
                    <a href="{{ route('doctor.patient.view', $emergency->patient->id) }}" target="_blank" class="mt-4 block text-center text-xs font-bold text-indigo-600 hover:underline">View Full Medical Records</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
