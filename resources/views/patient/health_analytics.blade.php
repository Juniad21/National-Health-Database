@extends('layouts.patient')

@section('header_title', 'Health Analytics')

@section('content')
<div class="space-y-8" x-data="{ showForm: false }">

    {{-- Summary Cards --}}
    @if(!$patient->height_cm)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-4 shadow-sm animate-pulse">
            <div class="bg-amber-100 p-2 rounded-xl text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-amber-900">Height Missing</h4>
                <p class="text-xs text-amber-700 mt-0.5">Please add your height in <a href="{{ route('patient.profile.edit') }}" class="font-black underline decoration-2 underline-offset-2">Profile Management</a> to enable auto-calculated BMI.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- BMI --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">BMI</p>
            <p class="text-3xl font-black text-gray-800">{{ $patient->bmi ?? '—' }}</p>
            @if($patient->bmi_category)
                @php
                    $bmiColors = ['Underweight' => 'bg-blue-100 text-blue-700', 'Normal' => 'bg-emerald-100 text-emerald-700', 'Overweight' => 'bg-amber-100 text-amber-700', 'Obese' => 'bg-red-100 text-red-700'];
                @endphp
                <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[10px] font-black {{ $bmiColors[$patient->bmi_category] ?? 'bg-gray-100 text-gray-600' }}">{{ $patient->bmi_category }}</span>
            @endif
        </div>

        {{-- Weight --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Weight</p>
            <p class="text-3xl font-black text-gray-800">{{ $latestMetric?->weight_kg ?? $patient->weight_kg ?? '—' }}</p>
            <p class="text-[10px] text-gray-400 mt-1">kg</p>
        </div>

        {{-- Blood Pressure --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Blood Pressure</p>
            @if($latestMetric?->systolic_bp)
                <p class="text-3xl font-black text-gray-800">{{ $latestMetric->systolic_bp }}/{{ $latestMetric->diastolic_bp }}</p>
                @php
                    $bpColors = ['Normal' => 'bg-emerald-100 text-emerald-700', 'Elevated' => 'bg-amber-100 text-amber-700', 'High' => 'bg-red-100 text-red-700', 'Critical' => 'bg-black text-white'];
                @endphp
                <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[10px] font-black {{ $bpColors[$latestMetric->bp_status] ?? 'bg-gray-100' }}">{{ $latestMetric->bp_status }}</span>
            @else
                <p class="text-3xl font-black text-gray-300">—</p>
            @endif
        </div>

        {{-- Heart Rate --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Heart Rate</p>
            <p class="text-3xl font-black text-gray-800">{{ $latestMetric?->heart_rate ?? '—' }}</p>
            <p class="text-[10px] text-gray-400 mt-1">bpm</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Glucose</p>
            <p class="text-2xl font-black text-gray-800">{{ $latestMetric?->glucose_level ?? '—' }}</p>
            <p class="text-[10px] text-gray-400">mg/dL</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">O₂ Saturation</p>
            <p class="text-2xl font-black text-gray-800">{{ $latestMetric?->oxygen_saturation ?? '—' }}</p>
            <p class="text-[10px] text-gray-400">%</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Temperature</p>
            <p class="text-2xl font-black text-gray-800">{{ $latestMetric?->temperature_c ?? '—' }}</p>
            <p class="text-[10px] text-gray-400">°C</p>
        </div>
    </div>

    {{-- Trend Visuals --}}
    @if($chartData->count() >= 2)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Weight Trend --}}
        @php $weightData = $chartData->whereNotNull('weight_kg'); @endphp
        @if($weightData->count() >= 2)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em] mb-4">Weight Trend</h3>
            <div class="space-y-2">
                @php $maxW = $weightData->max('weight_kg') ?: 1; @endphp
                @foreach($weightData as $d)
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-gray-400 w-16 shrink-0">{{ $d->recorded_at->format('M d') }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-teal-500 h-3 rounded-full transition-all" style="width: {{ ($d->weight_kg / $maxW) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-700 w-12 text-right">{{ $d->weight_kg }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- BP Trend --}}
        @php $bpData = $chartData->whereNotNull('systolic_bp'); @endphp
        @if($bpData->count() >= 2)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em] mb-4">Blood Pressure Trend</h3>
            <div class="space-y-2">
                @php $maxBP = $bpData->max('systolic_bp') ?: 1; @endphp
                @foreach($bpData as $d)
                    @php
                        $bpStatus = $d->bp_status;
                        $barColor = match($bpStatus) { 'Critical' => 'bg-black', 'High' => 'bg-red-500', 'Elevated' => 'bg-amber-500', default => 'bg-emerald-500' };
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-gray-400 w-16 shrink-0">{{ $d->recorded_at->format('M d') }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="{{ $barColor }} h-3 rounded-full transition-all" style="width: {{ ($d->systolic_bp / $maxBP) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-700 w-16 text-right">{{ $d->systolic_bp }}/{{ $d->diastolic_bp }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Heart Rate Trend --}}
        @php $hrData = $chartData->whereNotNull('heart_rate'); @endphp
        @if($hrData->count() >= 2)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em] mb-4">Heart Rate Trend</h3>
            <div class="space-y-2">
                @php $maxHR = $hrData->max('heart_rate') ?: 1; @endphp
                @foreach($hrData as $d)
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-gray-400 w-16 shrink-0">{{ $d->recorded_at->format('M d') }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-pink-500 h-3 rounded-full transition-all" style="width: {{ ($d->heart_rate / $maxHR) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-700 w-12 text-right">{{ $d->heart_rate }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Glucose Trend --}}
        @php $glData = $chartData->whereNotNull('glucose_level'); @endphp
        @if($glData->count() >= 2)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.2em] mb-4">Glucose Trend</h3>
            <div class="space-y-2">
                @php $maxGL = $glData->max('glucose_level') ?: 1; @endphp
                @foreach($glData as $d)
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-gray-400 w-16 shrink-0">{{ $d->recorded_at->format('M d') }}</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-purple-500 h-3 rounded-full transition-all" style="width: {{ ($d->glucose_level / $maxGL) * 100 }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-700 w-12 text-right">{{ $d->glucose_level }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Add Health Record --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-lg">Health Records</h3>
            <button @click="showForm = !showForm" class="bg-teal-600 text-white text-xs font-black px-4 py-2 rounded-xl hover:bg-teal-700 transition-all">
                <span x-text="showForm ? 'Cancel' : '+ Add Record'"></span>
            </button>
        </div>

        {{-- Form --}}
        <div x-show="showForm" x-transition class="p-6 border-b border-gray-100 bg-teal-50/30">
            <form action="{{ route('patient.health_metrics.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Date <span class="text-red-400">*</span></label>
                        <input type="date" name="recorded_at" value="{{ old('recorded_at', date('Y-m-d')) }}" required class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="65.0" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Systolic BP</label>
                        <input type="number" name="systolic_bp" value="{{ old('systolic_bp') }}" placeholder="120" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Diastolic BP</label>
                        <input type="number" name="diastolic_bp" value="{{ old('diastolic_bp') }}" placeholder="80" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Heart Rate</label>
                        <input type="number" name="heart_rate" value="{{ old('heart_rate') }}" placeholder="72" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Glucose (mg/dL)</label>
                        <input type="number" step="0.1" name="glucose_level" value="{{ old('glucose_level') }}" placeholder="100" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">O₂ Sat (%)</label>
                        <input type="number" step="0.1" name="oxygen_saturation" value="{{ old('oxygen_saturation') }}" placeholder="98" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Temp (°C)</label>
                        <input type="number" step="0.1" name="temperature_c" value="{{ old('temperature_c') }}" placeholder="36.6" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="How are you feeling today?" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2.5 rounded-xl font-black text-sm hover:bg-teal-700 transition-all">Save Record</button>
            </form>
        </div>

        {{-- History Table --}}
        <div class="p-6">
            @if($metrics->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="text-center py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Weight</th>
                                <th class="text-center py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">BP</th>
                                <th class="text-center py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">HR</th>
                                <th class="text-center py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Glucose</th>
                                <th class="text-center py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">BMI</th>
                                <th class="text-right py-3 px-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metrics as $m)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-2 font-bold text-gray-700">{{ $m->recorded_at->format('M d, Y') }}</td>
                                <td class="py-3 px-2 text-center">{{ $m->weight_kg ?? '—' }}</td>
                                <td class="py-3 px-2 text-center">
                                    @if($m->systolic_bp)
                                        {{ $m->systolic_bp }}/{{ $m->diastolic_bp }}
                                        @php
                                            $bpColors2 = ['Normal' => 'bg-emerald-100 text-emerald-700', 'Elevated' => 'bg-amber-100 text-amber-700', 'High' => 'bg-red-100 text-red-700', 'Critical' => 'bg-black text-white'];
                                        @endphp
                                        <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-black {{ $bpColors2[$m->bp_status] ?? '' }}">{{ $m->bp_status }}</span>
                                    @else — @endif
                                </td>
                                <td class="py-3 px-2 text-center">{{ $m->heart_rate ?? '—' }}</td>
                                <td class="py-3 px-2 text-center">{{ $m->glucose_level ?? '—' }}</td>
                                <td class="py-3 px-2 text-center font-bold">{{ $m->bmi ?? '—' }}</td>
                                <td class="py-3 px-2 text-right">
                                    <form action="{{ route('patient.health_metrics.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600 text-xs font-bold transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <h3 class="text-lg font-bold text-gray-500 mb-1">No health records yet</h3>
                    <p class="text-gray-400 text-sm mb-4">Start tracking your health by adding your first record.</p>
                    <button @click="showForm = true" class="bg-teal-600 text-white px-6 py-2.5 rounded-xl font-black text-sm hover:bg-teal-700 transition-all">+ Add First Record</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
