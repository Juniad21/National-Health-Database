@extends('layouts.patient')

@section('header_title', 'Medical Records')

@section('content')

    @php
        $categories = ['prescription' => 'Prescriptions', 'lab' => 'Lab Referrals', 'document' => 'Documents'];
    @endphp

    <div class="space-y-8">
        <!-- Hospital Lab Results (Real-time from Lab Orders) -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-indigo-900 px-8 py-5 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Hospital Lab Results</h3>
                <span class="bg-indigo-800 text-indigo-100 text-xs font-bold px-3 py-1 rounded-full border border-indigo-700">Verified</span>
            </div>

            <div class="p-8">
                @if(isset($labOrders) && $labOrders->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($labOrders as $lab)
                            <div x-data="{ open: false }" @click="open = !open"
                                class="border border-indigo-100 rounded-2xl p-5 hover:shadow-md transition-shadow bg-indigo-50/30 hover:bg-white cursor-pointer relative">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-100 text-indigo-700 rounded-xl">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                {{ $lab->updated_at->format('M d, Y') }}</p>
                                            <p class="font-bold text-gray-800">{{ $lab->labTestCatalog->test_name }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Results Out ✓</span>
                                </div>

                                <div class="flex flex-col mb-2">
                                    <p class="text-xs text-gray-500 font-medium">Hospital: <span class="text-indigo-600 font-bold">{{ $lab->hospital->name }}</span></p>
                                    <span class="text-xs text-indigo-600 font-bold mt-2 inline-flex items-center gap-1" x-text="open ? 'Hide Results ↑' : 'View Results ↓'"></span>
                                </div>

                                <div x-show="open" class="mt-4 pt-4 border-t border-indigo-100" style="display: none;" x-transition>
                                    <div class="mb-3">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Result Summary</h4>
                                        <p class="text-sm text-gray-800 font-bold bg-white p-3 rounded-lg border border-indigo-50">{{ $lab->result_summary }}</p>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        Requested by Dr. {{ $lab->doctor->first_name }} {{ $lab->doctor->last_name }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-400 font-medium">No verified lab results from hospitals yet.</p>
                    </div>
                @endif
            </div>
        </div>

        @foreach($categories as $type => $label)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-teal-900 px-8 py-5">
                    <h3 class="text-lg font-bold text-white">{{ $label }}</h3>
                </div>

                <div class="p-8">
                    @if(isset($records[$type]) && $records[$type]->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($records[$type] as $record)
                                <div x-data="{ open: false }" @click="open = !open"
                                    class="border border-gray-200 rounded-2xl p-5 hover:shadow-md transition-shadow bg-gray-50 hover:bg-white cursor-pointer relative">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-teal-100 text-teal-700 rounded-xl">
                                                @if($type == 'prescription')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                        </path>
                                                    </svg>
                                                @elseif($type == 'lab')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                    {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</p>
                                                <p class="font-bold text-gray-800">Dr. {{ $record->doctor->first_name }} {{ $record->doctor->last_name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col mb-2">
                                        <p class="text-gray-800 font-semibold text-sm" x-show="!open">{{ \Illuminate\Support\Str::limit($record->diagnosis, 60, '...') }}</p>
                                        <span class="text-xs text-teal-600 font-bold mt-1 inline-flex items-center gap-1" x-text="open ? 'Hide Details ↑' : 'View Details ↓'"></span>
                                    </div>

                                    <div x-show="open" class="mt-4 pt-4 border-t border-gray-200" style="display: none;" x-transition>
                                        <div class="mb-3">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ $type === 'prescription' ? 'Prescription' : ($type === 'lab' ? 'Test Name' : 'Title') }}</h4>
                                            <p class="text-sm text-gray-800 font-medium">{{ $record->diagnosis }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">{{ $type === 'prescription' ? 'Medicines & Dosages' : ($type === 'lab' ? 'Test Details' : 'Guidelines') }}</h4>
                                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $record->medications_or_results }}</p>
                                        </div>
                                        @if($record->document_path)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <a href="{{ Storage::url($record->document_path) }}" target="_blank" @click.stop
                                                    class="text-teal-600 font-semibold text-sm hover:text-teal-800 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    Download Attachment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-400 font-medium">No {{ strtolower($label) }} records uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection