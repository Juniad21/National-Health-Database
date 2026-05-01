@extends('layouts.patient')

@section('header_title', 'Overview & Analytics')

@section('content')

    <!-- Pre-Computation for active unrated appointments -->
    @php
        $unratedAppointment = \App\Models\Appointment::where('patient_id', Auth::user()->patient->id)
            ->where('status', 'completed')
            ->whereNotIn('id', \App\Models\DoctorEvaluation::pluck('appointment_id'))
            ->with('doctor')
            ->orderBy('updated_at', 'desc')
            ->first();
    @endphp


    <!-- Urgent Blood Alerts -->
    @if(isset($urgentBloodRequests) && count($urgentBloodRequests) > 0)
        @foreach($urgentBloodRequests as $request)
            <div
                class="bg-gradient-to-r from-red-600 to-rose-600 rounded-2xl p-6 shadow-lg shadow-red-200 mb-6 flex items-center justify-between text-white border border-red-500">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-wide">Emergency Request!</h3>
                        <p class="text-red-50 mt-1">Urgent: <strong>{{ $request->hospital->hospital_name }}</strong> desperately
                            needs <strong>{{ $request->blood_group_needed }}</strong> blood donors. Please contact them immediately.
                        </p>
                    </div>
                </div>
                <a href="#"
                    class="px-6 py-3 bg-white text-red-600 font-bold rounded-xl hover:bg-gray-50 hover:shadow-md transition-all">Respond
                    Now</a>
            </div>
        @endforeach
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Health Analytics Widget -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Health Trends
            </h3>

            @if($latestHealthMetric)
                <div class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-teal-50 rounded-2xl border border-teal-100">
                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">BMI</p>
                            <p class="text-xl font-black text-gray-800">{{ $latestHealthMetric->bmi ?? '—' }}</p>
                        </div>
                        <div class="text-center p-4 bg-teal-50 rounded-2xl border border-teal-100">
                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">BP</p>
                            <p class="text-xl font-black text-gray-800">{{ $latestHealthMetric->systolic_bp }}/{{ $latestHealthMetric->diastolic_bp }}</p>
                        </div>
                        <div class="text-center p-4 bg-teal-50 rounded-2xl border border-teal-100">
                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">Heart Rate</p>
                            <p class="text-xl font-black text-gray-800">{{ $latestHealthMetric->heart_rate ?? '—' }}<span class="text-[10px] ml-0.5">bpm</span></p>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <a href="{{ route('patient.health_analytics') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-white border-2 border-teal-600 text-teal-600 font-black text-sm rounded-2xl hover:bg-teal-50 transition-all">
                            View Full Health Analytics
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p>No health metrics recorded yet.</p>
                    <a href="{{ route('patient.health_analytics') }}" class="mt-4 text-teal-600 font-bold hover:underline">Get Started →</a>
                </div>
            @endif
        </div>

        <!-- Vaccination Tracking Widget -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
                Vaccination Tracker
            </h3>

            <div class="space-y-6">
                @if($upcomingVaccine)
                    <div class="p-6 bg-orange-50 border border-orange-100 rounded-3xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-20 h-20 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7 2a1 1 0 00-.707 1.707L14.586 11l-8.293 8.293A1 1 0 107.707 20.707l9-9a1 1 0 000-1.414l-9-9A1 1 0 007 2z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-1">Next Upcoming Vaccine</p>
                            <h4 class="text-2xl font-black text-gray-800">{{ $upcomingVaccine->vaccine_name }}</h4>
                            <p class="text-sm font-bold text-orange-700 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Due on {{ \Carbon\Carbon::parse($upcomingVaccine->due_date)->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-3xl text-center">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-emerald-800">All immunizations up to date.</p>
                        <p class="text-xs text-emerald-600 mt-1">No pending vaccines scheduled.</p>
                    </div>
                @endif

                <div class="pt-2">
                    <a href="#" class="w-full flex items-center justify-center gap-2 py-3 bg-white border-2 border-teal-600 text-teal-600 font-black text-sm rounded-2xl hover:bg-teal-50 transition-all">
                        View Vaccination Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Rating Modal -->
    @if($unratedAppointment)
        <div x-data="{ open: true }" x-show="open"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/75 backdrop-blur-sm">
            <div @click.away="open = false"
                class="relative w-full max-w-md p-6 bg-white rounded-3xl shadow-2xl transform transition-all">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800">Rate Your Visit</h3>
                    <p class="text-gray-500 text-sm mt-1">How was your recent consultation with Dr.
                        {{ $unratedAppointment->doctor->first_name }}?</p>
                </div>

                <form action="{{ route('patient.evaluation.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="appointment_id" value="{{ $unratedAppointment->id }}">

                    <div class="flex justify-center gap-2 mb-6" x-data="{ rating: 0, hoverRating: 0 }">
                        <input type="hidden" name="rating" x-model="rating">
                        <template x-for="i in 5">
                            <svg @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0"
                                class="w-10 h-10 cursor-pointer transition-colors"
                                :class="(hoverRating >= i || (!hoverRating && rating >= i)) ? 'text-yellow-400' : 'text-gray-200'"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </template>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Feedback (Optional)</label>
                        <textarea name="feedback_text" rows="3"
                            class="w-full border border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 text-sm shadow-sm"
                            placeholder="Tell us about your experience..."></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 shadow-md transition-all">
                        Submit Feedback
                    </button>
                    <button type="button" @click="open = false"
                        class="w-full py-2 mt-2 text-gray-500 font-semibold hover:text-gray-800 transition-colors text-sm">
                        Dismiss
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection