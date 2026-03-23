@extends('layouts.patient')

@section('header_title', 'Overview & Analytics')

@section('content')

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

            @if($healthMetrics->count() > 0)
                <div class="space-y-4">
                    @foreach($healthMetrics as $metric)
                        <div
                            class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl hover:bg-teal-50 transition-colors border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-2 h-10 bg-teal-400 rounded-full"></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($metric->recorded_date)->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 font-medium">Recorded Date</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-teal-700">{{ $metric->bmi }} <span
                                        class="text-xs text-teal-500 font-normal">BMI</span></p>
                                <p class="text-sm font-bold text-gray-600">{{ $metric->blood_pressure }} <span
                                        class="text-xs text-gray-400 font-normal">BP</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p>No health metrics recorded yet.</p>
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

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div>
                        <p class="font-bold text-gray-800">COVID-19 Booster</p>
                        <p class="text-sm text-green-600 font-medium">Completed: Jan 15, 2025</p>
                    </div>
                    <button
                        class="px-4 py-2 bg-white border border-gray-200 text-teal-600 text-sm font-semibold rounded-xl hover:bg-teal-50 hover:border-teal-200 transition-all shadow-sm">
                        Download Cert
                    </button>
                </div>

                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-2xl border border-orange-100">
                    <div>
                        <p class="font-bold text-gray-800">Influenza (Flu)</p>
                        <p class="text-sm text-orange-600 font-medium">Next Due: Nov 2026</p>
                    </div>
                    <button
                        class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-xl hover:bg-orange-700 transition-all shadow-sm shadow-orange-200">
                        Schedule
                    </button>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <div>
                        <p class="font-bold text-gray-800">Hepatitis B</p>
                        <p class="text-sm text-green-600 font-medium">Completed: Mar 10, 2024</p>
                    </div>
                    <button
                        class="px-4 py-2 bg-white border border-gray-200 text-teal-600 text-sm font-semibold rounded-xl hover:bg-teal-50 hover:border-teal-200 transition-all shadow-sm">
                        Download Cert
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection