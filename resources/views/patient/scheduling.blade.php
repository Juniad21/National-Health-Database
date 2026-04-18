@extends('layouts.patient')

@section('header_title', 'Appointment Scheduling & Search')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Search & Book Form -->
        <div class="lg:col-span-1 bg-white p-8 rounded-3xl shadow-sm border border-gray-100"
             x-data="{
                searchSpecialty: '',
                searchHospital: '',
                selectedDoctorId: null,
                selectedDoctorHospitalId: null,
                doctors: {{ Js::from($doctors->map(fn($d) => ['id' => $d->id, 'name' => 'Dr. '.$d->user->name, 'specialty' => $d->specialty ?? 'General', 'hospital' => $d->hospital->name, 'hospital_id' => $d->hospital->id])) }},
                get filteredDoctors() {
                    return this.doctors.filter(d => {
                        let matchSpecialty = true;
                        if (this.searchSpecialty.trim() !== '') {
                            matchSpecialty = d.specialty.toLowerCase().includes(this.searchSpecialty.toLowerCase());
                        }
                        let matchHospital = true;
                        if (this.searchHospital.trim() !== '') {
                            matchHospital = d.hospital.toLowerCase().includes(this.searchHospital.toLowerCase());
                        }
                        return matchSpecialty && matchHospital;
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

            <div class="mb-8 space-y-4">
                <div class="relative" x-data="{ open: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Doctor Specialty</label>
                    <input type="text" x-model="searchSpecialty" @focus="open = true" @click.away="open = false" placeholder="e.g. Cardiology"
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
                    <ul x-show="open && uniqueSpecialties.length > 0 && searchSpecialty !== ''" class="absolute z-10 w-full bg-white border border-gray-200 mt-1 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="spec in uniqueSpecialties.filter(s => s.toLowerCase().includes(searchSpecialty.toLowerCase()))" :key="spec">
                            <li @click="searchSpecialty = spec; open = false" class="px-4 py-2 hover:bg-teal-50 cursor-pointer text-sm text-gray-700" x-text="spec"></li>
                        </template>
                    </ul>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Hospital Name</label>
                    <input type="text" x-model="searchHospital" @focus="open = true" @click.away="open = false" placeholder="e.g. Square Hospital"
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
                    <ul x-show="open && uniqueHospitals.length > 0 && searchHospital !== ''" class="absolute z-10 w-full bg-white border border-gray-200 mt-1 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="hosp in uniqueHospitals.filter(h => h.toLowerCase().includes(searchHospital.toLowerCase()))" :key="hosp">
                            <li @click="searchHospital = hosp; open = false" class="px-4 py-2 hover:bg-teal-50 cursor-pointer text-sm text-gray-700" x-text="hosp"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <hr class="my-6 border-gray-100">

            <form method="POST" action="{{ route('patient.appointment.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Doctor</label>
                    <select name="doctor_id" x-model="selectedDoctorId" required
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                        <option value="" disabled selected>Choose a Doctor...</option>
                        <template x-for="doc in filteredDoctors" :key="doc.id">
                            <option :value="doc.id" x-text="doc.name + ' (' + doc.specialty + ') - ' + doc.hospital"></option>
                        </template>
                        <option value="" disabled x-show="filteredDoctors.length === 0">No doctors match your filters</option>
                    </select>
                    <input type="hidden" name="hospital_id" :value="selectedDoctorHospitalId">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                    <input type="date" name="appointment_date" required
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
                    return \Carbon\Carbon::parse($app->date)->isToday() && $app->status === 'pending';
                });
            @endphp

            @if($todayQueue->count() > 0)
                <div class="bg-gradient-to-br from-teal-500 to-emerald-600 p-8 rounded-3xl shadow-lg border border-teal-600 text-white relative overflow-hidden">
                    <!-- Decorative background elements -->
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 rounded-full bg-white opacity-10"></div>
                    <div class="absolute bottom-0 right-20 mb-[-20px] w-20 h-20 rounded-full bg-white opacity-10"></div>
                    
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2 relative z-10">
                        <span class="relative flex h-3 w-3 mr-1">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                        Live Queue Tracker
                    </h3>

                    <div class="space-y-4 relative z-10">
                        @foreach($todayQueue as $app)
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/20 flex flex-col sm:flex-row items-center justify-between">
                                <div>
                                    <p class="font-bold text-lg">Dr. {{ $app->doctor->last_name }} ({{ $app->time_slot }})</p>
                                    <p class="text-teal-100 text-sm flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $app->hospital->name }}
                                    </p>
                                </div>
                                <div class="mt-4 sm:mt-0 flex gap-4 text-center">
                                    <div class="bg-white text-teal-800 rounded-xl px-4 py-2 shadow-sm min-w-[100px]">
                                        <p class="text-xs font-bold uppercase tracking-wider text-teal-500 mb-0.5">Booking ID</p>
                                        <p class="text-xl font-black">{{ $app->booking_id ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white text-emerald-800 rounded-xl px-4 py-2 shadow-sm min-w-[100px]">
                                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-500 mb-0.5">Token No.</p>
                                        <p class="text-2xl font-black flex justify-center items-center gap-1">
                                            #{{ $app->token_number ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Active Appointments -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Upcoming & Past Appointments</h3>

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
                                            <div
                                                class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 text-xs font-bold">
                                                {{ substr($app->doctor->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Dr. {{ $app->doctor->last_name }}</p>
                                                <p class="text-xs text-teal-600">{{ $app->doctor->specialty ?? 'General' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-700 font-medium">{{ $app->hospital->name }}</td>
                                    <td class="py-4">
                                        @if($app->status === 'pending')
                                            <span
                                                class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Pending</span>
                                        @elseif($app->status === 'confirmed')
                                            <span
                                                class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Confirmed</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-bold rounded-full">{{ ucfirst($app->status) }}</span>
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