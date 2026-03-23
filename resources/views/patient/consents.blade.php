@extends('layouts.patient')

@section('header_title', 'Access Control')

@section('content')

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Manage Doctor Access</h2>
            <p class="text-gray-500 mt-2 text-sm">Control which doctors can view your medical history, prescriptions, and
                lab results.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 text-sm font-semibold text-gray-600 rounded-tl-xl">Doctor</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Specialty</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">Last Accessed</th>
                        <th class="p-4 text-sm font-semibold text-gray-600 text-right rounded-tr-xl">Access Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($doctors as $doctor)
                        @php
                            $consent = $consents->get($doctor->id);
                            $isGranted = $consent && $consent->status === 'granted';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold border border-teal-200">
                                        {{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">Dr. {{ $doctor->first_name }}
                                            {{ $doctor->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $doctor->doctor_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm font-medium text-teal-700">{{ $doctor->specialty ?? 'General' }}</td>
                            <td class="p-4 text-sm text-gray-500">
                                @if($consent && $consent->last_accessed_log)
                                    {{ $consent->last_accessed_log->format('M d, Y h:i A') }}
                                @else
                                    Never
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('patient.consent.update') }}">
                                    @csrf
                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                    <input type="hidden" name="status" value="{{ $isGranted ? 'revoked' : 'granted' }}">

                                    <!-- Toggle Button UI -->
                                    <button type="submit"
                                        class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 {{ $isGranted ? 'bg-teal-600' : 'bg-gray-200' }}">
                                        <span class="sr-only">Toggle access</span>
                                        <span
                                            class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform absolute shadow-sm {{ $isGranted ? 'translate-x-8' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection