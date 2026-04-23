@extends('layouts.patient')

@section('header_title', 'Symptom Assessment Tool')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="bg-teal-900 p-8 text-center text-white">
                <svg class="w-16 h-16 mx-auto mb-4 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
                <h2 class="text-3xl font-bold tracking-tight">How are you feeling?</h2>
                <p class="mt-2 text-teal-100 text-sm max-w-lg mx-auto">Describe your symptoms below, and our AI-assisted
                    tool will suggest the right medical specialty for your consultation.</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('patient.symptoms') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Describe your symptoms in detail</label>
                        <textarea name="symptoms" rows="5" required
                            placeholder="e.g. I have been experiencing a mild fever and severe headache for the past 2 days..."
                            class="w-full rounded-2xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm p-4 text-gray-700 bg-gray-50">{{ request('symptoms') }}</textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-teal-600 text-white font-bold py-4 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-200 transition-all text-lg">
                        Analyze Symptoms
                    </button>
                </form>

                @if(isset($suggestion))
                    <hr class="my-8 border-gray-100">
                    <div
                        class="bg-gradient-to-br from-teal-50 to-blue-50 border border-teal-100 rounded-2xl p-8 text-center shadow-inner">
                        <p class="text-sm font-bold uppercase tracking-widest text-teal-600 mb-2">Recommended Specialty</p>
                        <h3 class="text-2xl font-black text-gray-800">{{ $suggestion }}</h3>
                        <p class="mt-4 text-gray-600 text-sm max-w-md mx-auto">Based on your symptoms, we highly recommend
                            booking an appointment with a doctor specializing in <strong>{{ $suggestion }}</strong>.</p>

                        <a href="{{ route('patient.scheduling', ['specialty' => ($suggestion != 'General Practice' ? $suggestion : '')]) }}"
                            class="inline-block mt-6 px-8 py-3 bg-teal-900 text-white font-bold rounded-xl hover:bg-teal-800 transition-colors shadow-md">
                            Find Available Doctors
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection