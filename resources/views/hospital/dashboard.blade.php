@extends('layouts.hospital')

@section('header_title', 'Hospital Operations Center')

@section('emergency_banner')
    @if($emergencies->count() > 0)
        <!-- Intrusive Flashing Red Banner for Emergencies -->
        <div
            class="bg-red-600 text-white w-full py-4 px-6 shadow-2xl flex items-center justify-between border-b-4 border-red-800 animate-pulse relative z-50">
            <div class="flex items-center gap-4">
                <svg class="w-8 h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <div>
                    <h2 class="font-bold text-lg tracking-wider">CRITICAL: EMERGENCY ALERT TRIGGERED</h2>
                    <p class="text-red-100 text-sm font-medium">{{ $emergencies->first()->patient->first_name }}
                        {{ $emergencies->first()->patient->last_name }} reported an extreme medical emergency.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($emergencies->first()->status === 'active')
                    <form action="{{ route('hospital.emergencies.dispatch', $emergencies->first()->id) }}" method="POST">
                        @csrf
                        <button
                            class="px-5 py-2 bg-white text-red-700 font-bold rounded shadow hover:bg-red-50 transition-colors">Dispatch
                            Ambulance</button>
                    </form>
                @else
                    <span class="bg-red-800 px-3 py-1.5 rounded font-bold border border-red-500">Ambulance Dispatched</span>
                @endif

                @if($emergencies->first()->status === 'dispatched')
                    <form action="{{ route('hospital.emergencies.resolve', $emergencies->first()->id) }}" method="POST">
                        @csrf
                        <button
                            class="px-5 py-2 bg-gray-900 text-white font-bold rounded shadow hover:bg-black transition-colors">Mark
                            Resolved</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('content')
    <div class="space-y-8" x-data="hospitalDashboard()">

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