@extends('layouts.hospital')

@section('header_title', 'Hospital Operations Center')

@section('emergency_banner')
    @php
        // Show banner for any emergency that hasn't had an ambulance assigned or been resolved
        $activeEmergency = $emergencies->whereIn('status', ['Sent', 'Accepted'])->first();
    @endphp

    @if($activeEmergency)
        <!-- Intrusive Flashing Red Banner for Emergencies -->
        <div
            x-data="{ showBanner: true }"
            x-show="showBanner"
            class="fixed top-24 left-1/2 -translate-x-1/2 z-[60] w-full max-w-2xl px-4 animate-bounce">
            <div class="bg-red-600 text-white p-4 rounded-2xl shadow-2xl flex items-center justify-between border-b-4 border-red-800 relative">
                <div class="flex items-center gap-3">
                    <div class="bg-red-700 p-2 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-sm tracking-tight uppercase">Emergency Alert</h2>
                        <p class="text-red-100 text-[11px] font-medium leading-tight">
                            {{ $activeEmergency->patient->first_name }} reported a critical emergency.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form action="{{ route('hospital.emergencies.dispatch', $activeEmergency->id) }}" method="POST">
                        @csrf
                        <button class="px-3 py-1.5 bg-white text-red-700 text-[11px] font-black rounded-lg hover:bg-red-50 transition-all uppercase tracking-tighter">Dispatch</button>
                    </form>

                    <button @click="showBanner = false" class="p-1.5 hover:bg-red-700 rounded-lg transition-colors" title="Dismiss">
                        <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <div class="space-y-8" x-data="hospitalDashboard()">

        <!-- Quick Stats / Financial Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-2xl font-black text-gray-800">৳{{ number_format($stats['revenue'], 2) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Pending Bills</p>
                    <p class="text-2xl font-black text-gray-800">{{ $stats['pending_bills'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Active Claims</p>
                    <p class="text-2xl font-black text-gray-800">{{ $stats['active_claims'] }}</p>
                </div>
            </div>
        </div>

        <!-- Real-Time Resource Monitoring -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Resource Monitor
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($resources as $resource)
                    @php
                        $percentage = $resource->total_capacity > 0 ? round(($resource->currently_in_use / $resource->total_capacity) * 100) : 0;
                        $colorClass = $percentage >= 90 ? 'bg-red-500' : ($percentage >= 75 ? 'bg-orange-500' : 'bg-emerald-500');
                        $bgClass = $percentage >= 90 ? 'bg-red-50 border-red-100' : ($percentage >= 75 ? 'bg-orange-50 border-orange-100' : 'bg-white border-gray-100');
                    @endphp
                    <div class="{{ $bgClass }} rounded-2xl p-5 border shadow-sm transition-colors relative"
                        id="resource-{{ $resource->id }}">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="font-bold text-gray-700">{{ $resource->resource_type }}</h4>
                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $percentage }}%
                                Full</span>
                        </div>

                        <div class="text-3xl font-black text-gray-800 mb-1" id="usage-{{ $resource->id }}">
                            {{ $resource->currently_in_use }} <span class="text-sm font-medium text-gray-400">/
                                {{ $resource->total_capacity }}</span></div>

                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                            <div class="h-2.5 rounded-full {{ $colorClass }} transition-all duration-500"
                                style="width: {{ $percentage }}%" id="bar-{{ $resource->id }}"></div>
                        </div>

                        <div class="flex grid-cols-2 gap-2">
                            <button @click="updateResource({{ $resource->id }}, 'decrement')"
                                class="flex-1 py-1.5 bg-white border border-gray-200 rounded shadow-sm text-gray-600 hover:bg-gray-50 font-bold transition-colors">-</button>
                            <button @click="updateResource({{ $resource->id }}, 'increment')"
                                class="flex-1 py-1.5 bg-white border border-gray-200 rounded shadow-sm text-gray-600 hover:bg-gray-50 font-bold transition-colors">+</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Lab Processing Queue -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                    Pending Lab Tests Queue
                </h3>
                <span
                    class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full">{{ $pendingLabs->count() }}
                    Pending</span>
            </div>

            <div class="p-6">
                @if($pendingLabs->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 relative">
                        @foreach($pendingLabs as $lab)
                            <div
                                class="border border-gray-200 rounded-xl p-5 hover:border-purple-300 transition-colors shadow-sm bg-white">
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-bold text-gray-800 text-lg">{{ $lab->labTestCatalog->test_name }}</h4>
                                    <span
                                        class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded border border-amber-200">Pending</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-4 space-y-1">
                                    <p><strong class="text-gray-700">Patient:</strong> {{ $lab->patient->first_name }}
                                        {{ $lab->patient->last_name }} (NID: {{ $lab->patient->nid }})</p>
                                    <p><strong class="text-gray-700">Requested By:</strong> Dr. {{ $lab->doctor->first_name }}
                                        {{ $lab->doctor->last_name }}</p>
                                    <p><strong class="text-gray-700">Date Ordered:</strong>
                                        {{ \Carbon\Carbon::parse($lab->created_at)->format('M d, Y h:i A') }}</p>
                                </div>

                                <form action="{{ route('hospital.lab_orders.complete', $lab->id) }}" method="POST"
                                    class="mt-4 border-t border-gray-100 pt-4">
                                    @csrf
                                    <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">Result
                                        Summary</label>
                                    <textarea name="result_summary" rows="2" required placeholder="Enter lab test results here..."
                                        class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 mb-3"></textarea>
                                    <button type="submit"
                                        class="w-full py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm text-sm">
                                        Complete & Sync to Patient
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-500">No pending lab tests!</h3>
                        <p class="text-gray-400">All caught up with the queue.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function hospitalDashboard() {
            return {
                updateResource(id, action) {
                    fetch(`/hospital/resources/${id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ action: action })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Quick reload to update the UI accurately with PHP logic
                                window.location.reload();
                            }
                        });
                }
            }
        }
    </script>
@endsection