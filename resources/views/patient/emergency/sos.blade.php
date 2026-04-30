@extends('layouts.patient')

@section('content')
<div class="p-6" x-data="sosForm()">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">SOS Emergency Alert</h1>
            <p class="text-gray-600">Please provide details about the emergency. Your location will be shared automatically.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- SOS Form -->
            <div class="md:col-span-2">
                <form action="{{ route('patient.emergency.trigger') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                    @csrf
                    
                    <!-- Hidden Location Fields -->
                    <input type="hidden" name="latitude" x-model="location.lat">
                    <input type="hidden" name="longitude" x-model="location.lng">
                    <input type="hidden" name="address" x-model="location.address">

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Type</label>
                                <select name="emergency_type" required class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                                    <option value="">Select Type</option>
                                    <option value="Cardiac Arrest">Cardiac Arrest</option>
                                    <option value="Accident / Trauma">Accident / Trauma</option>
                                    <option value="Respiratory Distress">Respiratory Distress</option>
                                    <option value="Severe Bleeding">Severe Bleeding</option>
                                    <option value="Unconsciousness">Unconsciousness</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                                <select name="severity" required class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                                    <option value="medium">Medium</option>
                                    <option value="high">High / Urgent</option>
                                    <option value="critical">Critical / Life Threatening</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Symptoms or Short Note</label>
                            <textarea name="symptoms" rows="3" class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500" placeholder="e.g. Chest pain, difficulty breathing..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="text" name="contact_number" value="{{ Auth::user()->patient->phone }}" required class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact (Optional)</label>
                                <input type="text" name="guardian_contact" class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500" placeholder="Family member phone">
                            </div>
                        </div>

                        <!-- Location Status -->
                        <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div :class="location.lat ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'" class="p-2 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800" x-text="location.statusText"></p>
                                    <p class="text-xs text-gray-500" x-show="location.lat" x-text="'Lat: ' + location.lat + ', Lng: ' + location.lng"></p>
                                </div>
                            </div>
                            <button type="button" @click="getLocation()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Update Location</button>
                        </div>
                    </div>

                    <div class="p-6 bg-red-50 border-t border-red-100 flex items-center justify-between">
                        <p class="text-xs text-red-600 font-medium italic">By clicking Send SOS, you are requesting immediate medical assistance.</p>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-red-200 transition-all transform hover:scale-105">
                            SEND SOS ALERT
                        </button>
                    </div>
                </form>
            </div>

            <!-- Patient Info Card -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Patient Profile Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Full Name</span>
                            <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->patient->first_name }} {{ Auth::user()->patient->last_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Blood Group</span>
                            <span class="text-sm font-bold text-red-600">{{ Auth::user()->patient->blood_group }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">NID Number</span>
                            <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->patient->nid }}</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-50">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Known Conditions</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium">Hypertension</span>
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-medium">Diabetes Type 2</span>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg shadow-indigo-100 text-white">
                    <h3 class="font-bold mb-2">Emergency Hotline</h3>
                    <p class="text-indigo-100 text-sm mb-4">If the app is not responding, please call the national emergency service immediately.</p>
                    <a href="tel:999" class="block w-full text-center bg-white text-indigo-600 font-bold py-3 rounded-xl">Call 999</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sosForm() {
    return {
        location: {
            lat: null,
            lng: null,
            address: '',
            statusText: 'Waiting for location...'
        },
        init() {
            this.getLocation();
        },
        getLocation() {
            this.location.statusText = 'Fetching location...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.location.lat = position.coords.latitude;
                        this.location.lng = position.coords.longitude;
                        this.location.statusText = 'Location captured successfully';
                        // Reverse geocoding could be done here if an API key is available
                    },
                    (error) => {
                        console.error(error);
                        this.location.statusText = 'Geolocation denied. Please enter address manually if available.';
                    }
                );
            } else {
                this.location.statusText = 'Geolocation not supported by browser.';
            }
        }
    }
}
</script>
@endsection
