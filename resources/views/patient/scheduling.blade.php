@extends('layouts.patient')

@section('header_title', 'Appointment Explorer')

@section('content')

{{-- "DOCTOR CALLED YOU" NOTIFICATION BANNER --}}
@php
    $calledAppointments = $appointments->filter(function($app) {
        return $app->called_at && \Carbon\Carbon::parse($app->called_at)->isToday();
    });
@endphp

@if($calledAppointments->count() > 0)
    <div class="mb-8">
        @foreach($calledAppointments as $called)
            <div class="flex items-center gap-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-8 py-5 rounded-[2rem] shadow-xl shadow-emerald-200/50 mb-3 border border-white/20 backdrop-blur-md">
                <div class="flex-shrink-0 w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shadow-inner">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-xl tracking-tight leading-none mb-1">Dr. {{ $called->doctor->first_name }} {{ $called->doctor->last_name }} is ready!</p>
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest opacity-80">Check your medical records for new prescriptions.</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div x-data="{
    searchQuery: '',
    selectedSpecialty: 'All',
    selectedHospital: 'All',
    selectedDoctor: null,
    showBookingForm: false,
    doctors: {{ Js::from($doctors->map(fn($d) => [
        'id'          => $d->id,
        'name'        => 'Dr. ' . trim($d->first_name . ' ' . $d->last_name),
        'specialty'   => $d->specialty ?? 'General',
        'hospital'    => $d->hospital->name ?? 'Unknown',
        'hospital_id' => $d->hospital->id ?? null,
        'rating'      => (float)($d->reviews_avg_rating ?? 0),
        'reviews_count' => $d->reviews_count ?? 0,
        'bio'         => 'Highly experienced in ' . ($d->specialty ?? 'general medicine') . ' with a focus on patient-centric care.',
    ])) }},
    get filteredDoctors() {
        return this.doctors.filter(d => {
            let q = this.searchQuery.toLowerCase();
            let matchQ = q === '' || d.name.toLowerCase().includes(q) || d.specialty.toLowerCase().includes(q) || d.hospital.toLowerCase().includes(q);
            let matchS = this.selectedSpecialty === 'All' || d.specialty === this.selectedSpecialty;
            let matchH = this.selectedHospital === 'All' || d.hospital === this.selectedHospital;
            return matchQ && matchS && matchH;
        });
    },
    get uniqueSpecialties() {
        return ['All', ...new Set(this.doctors.map(d => d.specialty))];
    },
    get uniqueHospitals() {
        return ['All', ...new Set(this.doctors.map(d => d.hospital))];
    },
    selectDoctor(doc) {
        this.selectedDoctor = doc;
        this.showBookingForm = true;
        // Scroll to form if on mobile
        if(window.innerWidth < 1024) {
            document.getElementById('booking-section').scrollIntoView({ behavior: 'smooth' });
        }
    }
}" x-cloak class="space-y-8">

    <!-- Global Search & Filters -->
    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50">
        <div class="flex flex-col lg:flex-row gap-6 items-end">
            <div class="flex-1 w-full space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Search National Registry</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" 
                        class="w-full pl-14 pr-6 py-5 bg-slate-50 border-transparent rounded-3xl text-sm font-black text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all"
                        placeholder="Search by doctor, specialty, or facility name...">
                </div>
            </div>
            
            <div class="w-full lg:w-48 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Specialty</label>
                <select x-model="selectedSpecialty" class="w-full py-5 px-6 bg-slate-50 border-transparent rounded-3xl text-sm font-black text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                    <template x-for="spec in uniqueSpecialties" :key="spec">
                        <option :value="spec" x-text="spec"></option>
                    </template>
                </select>
            </div>

            <div class="w-full lg:w-64 space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Healthcare Facility</label>
                <select x-model="selectedHospital" class="w-full py-5 px-6 bg-slate-50 border-transparent rounded-3xl text-sm font-black text-slate-700 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all">
                    <template x-for="hosp in uniqueHospitals" :key="hosp">
                        <option :value="hosp" x-text="hosp"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Doctor Results -->
        <div :class="showBookingForm ? 'lg:col-span-2' : 'lg:col-span-3'" class="space-y-6 transition-all duration-500">
            <div class="flex items-center justify-between px-4">
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Available Specialists <span class="text-slate-300 ml-2 text-sm font-bold" x-text="'(' + filteredDoctors.length + ' found)'"></span></h3>
                <div class="flex gap-2">
                    <div class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Real-time Directory</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <template x-for="doc in filteredDoctors" :key="doc.id">
                    <div @click="selectDoctor(doc)" 
                        class="bg-white p-6 rounded-[2rem] border border-slate-100 hover:border-teal-500 hover:shadow-2xl hover:shadow-teal-900/5 transition-all cursor-pointer group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-full -mr-12 -mt-12 group-hover:bg-teal-100 transition-colors opacity-50"></div>
                        
                        <div class="flex gap-5 relative z-10">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-teal-600 font-black text-2xl shadow-inner group-hover:bg-teal-600 group-hover:text-white transition-all">
                                <span x-text="doc.name.split(' ').length > 1 ? doc.name.split(' ')[1][0] : doc.name[0]"></span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-slate-800 text-lg group-hover:text-teal-700 transition-colors" x-text="doc.name"></h4>
                                <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-2" x-text="doc.specialty"></p>
                                <div class="flex items-center gap-1 text-xs text-slate-400 font-bold">
                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-slate-700" x-text="parseFloat(doc.rating).toFixed(1)"></span>
                                    <span class="opacity-50" x-text="'(' + doc.reviews_count + ' reviews)'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 pt-5 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-[10px] font-black uppercase tracking-widest truncate max-w-[120px]" x-text="doc.hospital"></span>
                            </div>
                            <span class="text-[10px] font-black text-teal-600 uppercase tracking-widest group-hover:translate-x-1 transition-transform">Book Now →</span>
                        </div>
                    </div>
                </template>
                
                <div x-show="filteredDoctors.length === 0" class="col-span-full py-20 text-center bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-slate-400 font-black uppercase tracking-widest text-[10px]">No matches found in national directory</p>
                </div>
            </div>

            <!-- All Appointments Table -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-50">
                <h3 class="text-xl font-black text-slate-800 tracking-tight mb-8 flex items-center gap-3">
                    Your Scheduled Consultations
                    <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $appointments->count() }} Total</span>
                </h3>

                @if($appointments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                    <th class="pb-4 px-4">Consultation Time</th>
                                    <th class="pb-4 px-4">Medical Specialist</th>
                                    <th class="pb-4 px-4">Facility</th>
                                    <th class="pb-4 px-4">Status</th>
                                    <th class="pb-4 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($appointments as $app)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="py-5 px-4">
                                            <p class="font-black text-slate-800 text-sm">
                                                {{ \Carbon\Carbon::parse($app->date)->format('M d, Y') }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $app->time_slot }}</p>
                                        </td>
                                        <td class="py-5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-black text-sm border border-teal-100 shadow-sm">
                                                    {{ substr($app->doctor->first_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-800 text-sm">
                                                        <a href="{{ route('doctor.public_profile', $app->doctor_id) }}" class="hover:text-teal-600 transition-colors">
                                                            Dr. {{ $app->doctor->first_name }} {{ $app->doctor->last_name }}
                                                        </a>
                                                    </p>
                                                    <p class="text-[10px] font-black text-teal-500 uppercase tracking-widest">{{ $app->doctor->specialty ?? 'General Medicine' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-5 px-4">
                                            <span class="text-xs font-bold text-slate-600">{{ $app->hospital->name }}</span>
                                        </td>
                                        <td class="py-5 px-4">
                                            @php
                                                $status = strtolower($app->status);
                                                $statusClasses = match($status) {
                                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                    'approved', 'confirmed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'completed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                                };
                                            @endphp
                                            <span class="px-3 py-1.5 {{ $statusClasses }} border rounded-xl text-[9px] font-black uppercase tracking-widest">
                                                {{ $app->status }}
                                            </span>
                                        </td>
                                        <td class="py-5 px-4 text-right">
                                            @if($status === 'completed' && !$app->evaluation)
                                                <button @click="$dispatch('open-review-modal', { id: {{ $app->id }}, doctor: 'Dr. {{ $app->doctor->first_name }} {{ $app->doctor->last_name }}' })" 
                                                    class="text-[9px] font-black text-teal-600 hover:text-white hover:bg-teal-600 border border-teal-100 px-4 py-2 rounded-xl transition-all uppercase tracking-widest">
                                                    Rate Care
                                                </button>
                                            @elseif($app->evaluation)
                                                <div class="flex items-center justify-end gap-1">
                                                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Rated {{ $app->evaluation->rating_1_to_5 }}</span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16 bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No historical clinical visits</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Booking -->
        <div class="lg:col-span-1" id="booking-section">
            
            <div x-show="showBookingForm" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" 
                class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-slate-900/40 sticky top-8">
                <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-10">
                        <h4 class="text-2xl font-black tracking-tight leading-none">Book Visit</h4>
                        <button @click="showBookingForm = false; selectedDoctor = null" class="p-2 text-slate-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="mb-10 p-6 bg-white/5 rounded-3xl border border-white/10 flex items-center gap-5">
                        <div class="w-14 h-14 bg-teal-500 rounded-2xl flex items-center justify-center font-black text-white text-xl shadow-lg shadow-teal-500/20">
                            <span x-text="selectedDoctor?.name.split(' ').length > 1 ? selectedDoctor?.name.split(' ')[1][0] : (selectedDoctor?.name ? selectedDoctor?.name[0] : '')"></span>
                        </div>
                        <div>
                            <p class="font-black text-lg leading-none mb-1" x-text="selectedDoctor?.name"></p>
                            <p class="text-[10px] font-black text-teal-400 uppercase tracking-widest" x-text="selectedDoctor?.specialty"></p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('patient.appointment.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="doctor_id" :value="selectedDoctor?.id">
                        <input type="hidden" name="hospital_id" :value="selectedDoctor?.hospital_id">

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Select Date</label>
                            <input type="date" name="date" required
                                class="w-full bg-white/5 border-white/10 rounded-2xl py-5 px-6 text-sm font-black text-white focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-4">Preferred Slot</label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="slot in ['09:00 AM', '10:00 AM', '11:30 AM', '02:00 PM', '04:00 PM', '06:30 PM']">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="time_slot" :value="slot" class="hidden peer" required>
                                        <div class="py-3 text-center rounded-xl border border-white/5 bg-white/5 group-hover:bg-white/10 peer-checked:bg-teal-500 peer-checked:border-teal-500 transition-all">
                                            <span class="text-[9px] font-black uppercase tracking-widest" x-text="slot"></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full py-6 bg-teal-500 text-white font-black rounded-3xl hover:bg-teal-400 shadow-2xl shadow-teal-500/30 transition-all uppercase tracking-widest text-xs">
                                Confirm Appointment
                            </button>
                            <p class="text-center text-[9px] font-bold text-slate-500 mt-6 uppercase tracking-widest">A token will be issued upon approval</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div x-data="{ 
    isOpen: false, 
    appointmentId: null, 
    doctorName: '', 
    rating: 5, 
    comment: '' 
}" 
x-show="isOpen" 
x-on:open-review-modal.window="isOpen = true; appointmentId = $event.detail.id; doctorName = $event.detail.doctor"
class="fixed inset-0 z-[100] overflow-y-auto" 
x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="isOpen = false"></div>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-[3rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-50">
            <div class="bg-white px-10 pt-10 pb-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-none mb-1">Clinic Feedback</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rate Dr. <span class="text-teal-600" x-text="doctorName.split(' ')[1]"></span></p>
                    </div>
                </div>

                <form action="{{ route('patient.evaluation.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="appointment_id" :value="appointmentId">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-4">Clinical Quality Rating</label>
                        <div class="flex gap-3 justify-center bg-slate-50 p-6 rounded-[2rem]">
                            <template x-for="i in 5">
                                <button type="button" @click="rating = i" class="transition-transform transform hover:scale-125 focus:outline-none">
                                    <svg class="w-12 h-12 transition-colors duration-300" :class="i <= rating ? 'text-amber-400 fill-current' : 'text-slate-200'" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Personal Experience</label>
                        <textarea name="feedback_text" rows="4" placeholder="How was the communication and treatment?" 
                            class="w-full rounded-3xl border-transparent bg-slate-50 text-sm font-black focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all py-5 px-6"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="isOpen = false" class="flex-1 px-8 py-5 border-2 border-slate-50 text-slate-400 font-black rounded-3xl hover:bg-slate-50 transition-all uppercase tracking-widest text-[10px]">Close</button>
                        <button type="submit" class="flex-1 px-8 py-5 bg-teal-600 text-white font-black rounded-3xl hover:bg-teal-700 shadow-xl shadow-teal-100 transition-all uppercase tracking-widest text-[10px]">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection