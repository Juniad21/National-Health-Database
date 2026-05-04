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
                    <a href="{{ route('patient.vaccinations') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-white border-2 border-teal-600 text-teal-600 font-black text-sm rounded-2xl hover:bg-teal-50 transition-all">
                        View Vaccination Details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Premium Rating Modal -->
    @if($unratedAppointment)
        <div x-data="{ open: true, rating: 0, hoverRating: 0, step: 1 }" x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 backdrop-blur-none"
            x-transition:enter-end="opacity-100 backdrop-blur-md"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 backdrop-blur-md"
            x-transition:leave-end="opacity-0 backdrop-blur-none"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4">
            
            <div @click.away="open = false"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                class="relative w-full max-w-lg bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/50 overflow-hidden">
                
                {{-- Decorative Background --}}
                <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-teal-400 to-emerald-500 opacity-20 blur-2xl rounded-t-[2rem]"></div>
                
                <div class="p-8 relative z-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-teal-400 to-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-teal-500/30 transform transition-transform hover:scale-110 duration-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">How was your visit?</h3>
                        <p class="text-gray-500 mt-2 font-medium">Your recent consultation with <span class="font-bold text-teal-700">Dr. {{ $unratedAppointment->doctor->first_name }} {{ $unratedAppointment->doctor->last_name }}</span></p>
                    </div>

                    <form action="{{ route('patient.evaluation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $unratedAppointment->id }}">
                        <input type="hidden" name="rating" x-model="rating">

                        {{-- Step 1: Star Rating --}}
                        <div x-show="step === 1" x-transition.opacity.duration.300ms class="space-y-6">
                            <div class="flex justify-center gap-3">
                                <template x-for="i in 5">
                                    <svg @click="rating = i; setTimeout(() => step = 2, 400)" 
                                         @mouseenter="hoverRating = i" 
                                         @mouseleave="hoverRating = 0"
                                        class="w-12 h-12 cursor-pointer transition-all duration-300 transform"
                                        :class="{
                                            'text-yellow-400 scale-110 drop-shadow-md': hoverRating >= i || (!hoverRating && rating >= i),
                                            'text-gray-200 hover:text-yellow-200': hoverRating < i && (!hoverRating && rating < i)
                                        }"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </template>
                            </div>
                            <div class="text-center h-6">
                                <p x-show="hoverRating === 1 || (!hoverRating && rating === 1)" class="text-rose-500 font-bold animate-pulse">Needs Improvement</p>
                                <p x-show="hoverRating === 2 || (!hoverRating && rating === 2)" class="text-orange-500 font-bold animate-pulse">Fair</p>
                                <p x-show="hoverRating === 3 || (!hoverRating && rating === 3)" class="text-yellow-600 font-bold animate-pulse">Good</p>
                                <p x-show="hoverRating === 4 || (!hoverRating && rating === 4)" class="text-teal-600 font-bold animate-pulse">Very Good</p>
                                <p x-show="hoverRating === 5 || (!hoverRating && rating === 5)" class="text-emerald-500 font-bold animate-pulse">Exceptional Care!</p>
                            </div>
                            <button type="button" @click="open = false" class="w-full py-3 mt-4 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                                Skip for now
                            </button>
                        </div>

                        {{-- Step 2: Written Feedback --}}
                        <div x-show="step === 2" style="display: none;" x-transition.opacity.duration.300ms>
                            <div class="bg-gray-50/50 rounded-2xl p-1 mb-6 border border-gray-100">
                                <textarea name="feedback_text" rows="4"
                                    class="w-full bg-transparent border-0 focus:ring-0 text-gray-700 placeholder-gray-400 resize-none px-4 py-3 font-medium"
                                    placeholder="What made your experience good or bad? Any details help us improve."></textarea>
                            </div>
                            
                            <div class="flex gap-4">
                                <button type="button" @click="step = 1" class="px-6 py-4 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors">
                                    Back
                                </button>
                                <button type="submit" class="flex-1 py-4 bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-black rounded-xl hover:from-teal-600 hover:to-emerald-600 shadow-lg shadow-teal-500/30 transform hover:-translate-y-0.5 transition-all">
                                    Publish Feedback
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection