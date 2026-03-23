@extends('layouts.patient')

@section('header_title', 'Appointment Scheduling & Search')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Search & Book Form -->
        <div class="lg:col-span-1 bg-white p-8 rounded-3xl shadow-sm border border-gray-100"
             x-data="{
                searchSpecialty: '',
                searchHospital: '',
                doctors: {{ Js::from($doctors->map(fn($d) => ['id' => $d->id, 'name' => 'Dr. '.$d->first_name.' '.$d->last_name, 'specialty' => $d->specialty ?? 'General', 'hospital' => $d->hospital->name])) }},
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
             }">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Book an Appointment</h3>

            <div class="mb-8 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Doctor Specialty</label>
                    <input type="text" x-model="searchSpecialty" list="specialtiesList" placeholder="e.g. Cardiology"
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
                    <datalist id="specialtiesList">
                        <template x-for="spec in uniqueSpecialties" :key="spec">
                            <option :value="spec"></option>
                        </template>
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Hospital Name</label>
                    <input type="text" x-model="searchHospital" list="hospitalsList" placeholder="e.g. Square Hospital"
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm text-sm">
                    <datalist id="hospitalsList">
                        <template x-for="hosp in uniqueHospitals" :key="hosp">
                            <option :value="hosp"></option>
                        </template>
                    </datalist>
                </div>
            </div>

            <hr class="my-6 border-gray-100">

            <form method="POST" action="{{ route('patient.appointment.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Doctor</label>
                    <select name="doctor_id" required
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                        <option value="" disabled selected>Choose a Doctor...</option>
                        <template x-for="doc in filteredDoctors" :key="doc.id">
                            <option :value="doc.id" x-text="doc.name + ' (' + doc.specialty + ') - ' + doc.hospital"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Hospital</label>
                    <select name="hospital_id" required
                        class="w-full rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                        <option value="" disabled selected>Choose a Hospital...</option>
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                        @endforeach
                    </select>
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

        <!-- Active Appointments -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
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
                                            {{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}</p>
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
@endsection