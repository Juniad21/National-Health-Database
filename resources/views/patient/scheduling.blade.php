@extends('layouts.patient')

@section('header_title', 'Appointment Scheduling & Search')

@section('content')

{{-- ============================================================ --}}
{{-- "DOCTOR CALLED YOU" NOTIFICATION BANNER --}}
{{-- ============================================================ --}}
@php
    $calledAppointments = $appointments->filter(function($app) {
        return $app->called_at && \Carbon\Carbon::parse($app->called_at)->isToday();
    });
@endphp

@if($calledAppointments->count() > 0)
    <div class="mb-6 animate-bounce-once">
        @foreach($calledAppointments as $called)
            <div class="flex items-center gap-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-4 rounded-2xl shadow-lg mb-3">
                <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-lg">🔔 Dr. {{ $called->doctor->first_name }} {{ $called->doctor->last_name }} has called you!</p>
                    <p class="text-emerald-100 text-sm">Your consultation at {{ $called->hospital->name }} was completed today. Check your medical records for prescriptions & lab orders.</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Search & Book Form -->
    <div class="lg:col-span-1 bg-white p-8 rounded-3xl shadow-sm border border-gray-100"
         x-data="{
            searchQuery: '',
            searchHospital: '',
            selectedDoctorId: null,
            selectedDoctorHospitalId: null,
            doctors: {{ Js::from($doctors->map(fn($d) => [
                'id'          => $d->id,
                'name'        => 'Dr. ' . trim($d->first_name . ' ' . $d->last_name),
                'specialty'   => $d->specialty ?? 'General',
                'hospital'    => $d->hospital->name ?? 'Unknown',
                'hospital_id' => $d->hospital->id ?? null,
            ])) }},
            get filteredDoctors() {
                return this.doctors.filter(d => {
                    let q = this.searchQuery.trim().toLowerCase();
                    let h = this.searchHospital.trim().toLowerCase();
                    let matchQ = q === '' || d.name.toLowerCase().includes(q) || d.specialty.toLowerCase().includes(q);
                    let matchH = h === '' || d.hospital.toLowerCase().includes(h);
                    return matchQ && matchH;
                });
            },
            get uniqueSpecialties() {
                return [...new Set(this.doctors.map(d => d.specialty))];
            },
            get uniqueHospitals() {
                return [...new Set(this.doctors.map(d => d.hospital))];
            }
         }"
         x-effect="selectedDoctorHospitalId = (doctors.find(d => d.id == selectedDoctorId) || {}).hospital_id || null">

        <h3 class="text-lg font-bold text-gray-800 mb-6">Book an Appointment</h3>

        {{-- Search Filters --}}
        <div class="mb-6 space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Doctor Name or Specialty</label>
                <input type="text" x-model="searchQuery" placeholder="e.g. Kamal or Cardiology"
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
            </div>
            <div class="relative" x-data="{ open: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Hospital Name</label>
                <input type="text" x-model="searchHospital" @focus="open = true" @click.away="open = false"
                    placeholder="e.g. United Hospital"
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
                <ul x-show="open && uniqueHospitals.length > 0 && searchHospital !== ''"
                    class="absolute z-10 w-full bg-white border border-gray-200 mt-1 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                    <template x-for="hosp in uniqueHospitals.filter(h => h.toLowerCase().includes(searchHospital.toLowerCase()))" :key="hosp">
                        <li @click="searchHospital = hosp; open = false"
                            class="px-4 py-2 hover:bg-teal-50 cursor-pointer text-sm text-gray-700" x-text="hosp"></li>
                    </template>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-gray-100">

        <form method="POST" action="{{ route('patient.appointment.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Select Doctor</label>
                <select name="doctor_id" x-model="selectedDoctorId" required
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                    <option value="" disabled selected>Choose a Doctor...</option>
                    <template x-for="doc in filteredDoctors" :key="doc.id">
                        <option :value="doc.id" x-text="doc.name + ' (' + doc.specialty + ') — ' + doc.hospital"></option>
                    </template>
                    <option value="" disabled x-show="filteredDoctors.length === 0">No doctors match your filters</option>
                </select>
                <input type="hidden" name="hospital_id" :value="selectedDoctorHospitalId">
                <p class="text-xs text-gray-400 mt-1" x-show="selectedDoctorId">
                    Lab tests will be conducted at the doctor's hospital.
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Time Slot</label>
                <select name="time_slot" required
                    class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                    <option value="" disabled selected>Select Time...</option>
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="11:30 AM">11:30 AM</option>
                    <option value="02:00 PM">02:00 PM</option>
                    <option value="04:00 PM">04:00 PM</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-teal-600 text-white font-bold py-3 rounded-xl hover:bg-teal-700 shadow-md shadow-teal-200 transition-all mt-4">
                Book Appointment
            </button>
        </form>
    </div>

    <!-- Appointments Column -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Live Queue Tracker (Today) -->
        @php
            $todayQueue = $appointments->filter(function($app) {
                $status = strtolower($app->status);
                return \Carbon\Carbon::parse($app->date)->isToday()
                    && ($status === 'pending' || $status === 'approved');
            });
        @endphp

        @if($todayQueue->count() > 0)
            <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-8 rounded-3xl shadow-lg border border-teal-600 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 rounded-full bg-white opacity-10"></div>
                <div class="absolute bottom-0 right-20 mb-[-20px] w-20 h-20 rounded-full bg-white opacity-10"></div>

                <h3 class="text-xl font-bold mb-6 flex items-center gap-2 relative z-10">
                    <span class="relative flex h-3 w-3 mr-1">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                    </span>
                    Live Queue Tracker — Today
                </h3>

                <div class="space-y-4 relative z-10">
                    @foreach($todayQueue as $app)
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <p class="font-bold text-lg">Dr. {{ $app->doctor->first_name }} {{ $app->doctor->last_name }}</p>
                                <p class="text-teal-100 text-sm mt-0.5">{{ $app->time_slot }} &bull; {{ $app->hospital->name }}</p>
                                @if(strtolower($app->status) === 'pending')
                                    <span class="inline-block mt-2 px-3 py-1 bg-yellow-400/30 text-yellow-100 border border-yellow-300/40 text-xs font-bold rounded-full">
                                        ⏳ Awaiting Doctor Approval
                                    </span>
                                @elseif(strtolower($app->status) === 'approved')
                                    <span class="inline-block mt-2 px-3 py-1 bg-green-400/30 text-green-100 border border-green-300/40 text-xs font-bold rounded-full">
                                        ✅ Approved — You are in the queue!
                                    </span>
                                @endif
                            </div>
                            <div class="flex gap-3 text-center">
                                <div class="bg-white text-teal-800 rounded-xl px-4 py-2 shadow-sm min-w-[110px]">
                                    <p class="text-xs font-bold uppercase tracking-wider text-teal-500 mb-0.5">Booking ID</p>
                                    <p class="text-sm font-black break-all">{{ $app->booking_id ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white text-emerald-800 rounded-xl px-4 py-2 shadow-sm min-w-[90px]">
                                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-500 mb-0.5">Token No.</p>
                                    <p class="text-2xl font-black flex justify-center items-center">
                                        @if(strtolower($app->status) === 'approved' && $app->token_number)
                                            {{ $app->token_number }}
                                        @else
                                            <span class="text-sm text-gray-400">Pending</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Appointments Table -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-6">All Appointments</h3>

            @if($appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pb-3 text-sm font-semibold text-gray-500">Date & Time</th>
                                <th class="pb-3 text-sm font-semibold text-gray-500">Doctor</th>
                                <th class="pb-3 text-sm font-semibold text-gray-500">Hospital</th>
                                <th class="pb-3 text-sm font-semibold text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($appointments as $app)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4">
                                        <p class="font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($app->date)->format('M d, Y') }}</p>
                                        <p class="text-sm text-gray-500">{{ $app->time_slot }}</p>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 text-xs font-bold">
                                                {{ substr($app->doctor->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">
                                                    <a href="{{ route('doctor.public_profile', $app->doctor_id) }}" class="hover:text-teal-600 transition-colors">
                                                        Dr. {{ $app->doctor->first_name }} {{ $app->doctor->last_name }}
                                                    </a>
                                                </p>
                                                <p class="text-xs text-teal-600">{{ $app->doctor->specialty ?? 'General' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-700 font-medium">{{ $app->hospital->name }}</td>
                                    <td class="py-4">
                                        @if(strtolower($app->status) === 'pending')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Pending</span>
                                        @elseif(strtolower($app->status) === 'approved' || strtolower($app->status) === 'confirmed')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Approved</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full">{{ ucfirst($app->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                    <p class="text-gray-500 font-medium">You have no appointments booked yet.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection