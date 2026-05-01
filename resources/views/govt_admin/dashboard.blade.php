@extends('layouts.govt_admin')

@section('header_title', 'Government Health Administration Dashboard')

@section('content')
    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">District</label>
            <select class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option>All Districts</option>
                <option>Dhaka</option>
                <option>Chittagong</option>
                <option>Rajshahi</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hospital Type</label>
            <select class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option>All Types</option>
                <option>Government</option>
                <option>Private</option>
                <option>Specialized</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Date Range</label>
            <input type="date" class="w-full border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="flex items-end">
            <button class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Overview Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Registered Doctors</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['registered_doctors'] }}</h3>
                    <p class="text-xs text-green-600 font-medium mt-2">↑ 12% from last month</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Verifications</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['pending_verifications'] }}</h3>
                    <p class="text-xs text-amber-600 font-medium mt-2">Action required</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Registered Hospitals</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['registered_hospitals'] }}</h3>
                    <p class="text-xs text-green-600 font-medium mt-2">Across 64 districts</p>
                </div>
                <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Patients</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['active_patients'] }}</h3>
                    <p class="text-xs text-blue-600 font-medium mt-2">National database</p>
                </div>
                <div class="p-3 bg-teal-50 rounded-xl text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Doctor Verification Panel -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Doctor Verification Queue</h3>
                <a href="{{ route('govt_admin.doctors.index') }}" class="text-indigo-600 text-sm font-semibold hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Doctor Name</th>
                            <th class="px-6 py-4">BMDC ID</th>
                            <th class="px-6 py-4">Specialty</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingDoctors as $doctor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $doctor['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $doctor['hospital'] }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor['license'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor['specialty'] }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'Pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'Approved' => 'bg-green-50 text-green-600 border-green-100',
                                        'Rejected' => 'bg-red-50 text-red-600 border-red-100',
                                        'Needs Review' => 'bg-blue-50 text-blue-600 border-blue-100'
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium border {{ $statusClasses[$doctor['status']] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $doctor['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button title="View Documents" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button title="Approve" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Incident and Compliance Alerts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Critical Alerts</h3>
                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">3 New</span>
            </div>
            <div class="p-6 flex-1 space-y-4">
                @foreach($alerts as $alert)
                <div class="flex gap-4 p-4 rounded-xl border {{ $alert['severity'] == 'Critical' ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100' }}">
                    <div class="mt-1">
                        @if($alert['severity'] == 'Critical')
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="text-sm font-bold text-gray-800">{{ $alert['title'] }}</h4>
                            <span class="text-[10px] font-bold uppercase {{ $alert['severity'] == 'Critical' ? 'text-red-600' : 'text-amber-600' }}">{{ $alert['severity'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $alert['target'] }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-[10px] text-gray-400 italic">{{ $alert['time'] }}</span>
                            <button class="text-xs font-bold text-indigo-600 hover:underline">Take Action</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="p-4 border-t border-gray-50 text-center">
                <a href="#" class="text-sm font-semibold text-gray-500 hover:text-indigo-600">View All Alerts</a>
            </div>
        </div>
    </div>

    <!-- Hospital Monitoring Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Hospital Operational Status</h3>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex gap-2">
                    <span class="flex items-center gap-1 text-xs text-gray-500"><span class="w-2 h-2 rounded-full bg-green-500"></span> Normal</span>
                    <span class="flex items-center gap-1 text-xs text-gray-500"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Warning</span>
                    <span class="flex items-center gap-1 text-xs text-gray-500"><span class="w-2 h-2 rounded-full bg-red-500"></span> Critical</span>
                </div>
                <a href="{{ route('govt_admin.hospitals.index') }}" class="text-indigo-600 text-sm font-semibold hover:underline">View All</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Hospital</th>
                        <th class="px-6 py-4">District</th>
                        <th class="px-6 py-4">Bed Occ.</th>
                        <th class="px-6 py-4 text-center">ICU</th>
                        <th class="px-6 py-4 text-center">Vents</th>
                        <th class="px-6 py-4">Blood Bank</th>
                        <th class="px-6 py-4">Compliance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($hospitals as $hospital)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $hospital['name'] }}</div>
                            <div class="text-[10px] text-indigo-600 font-bold uppercase">{{ $hospital['type'] }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $hospital['district'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ intval($hospital['beds']) > 90 ? 'bg-red-500' : (intval($hospital['beds']) > 70 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ $hospital['beds'] }}"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-700">{{ $hospital['beds'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold {{ $hospital['icu'] == '0/15' || $hospital['icu'] == '0/50' ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $hospital['icu'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-xs font-bold text-gray-700">{{ $hospital['vent'] }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $hospital['blood'] == 'Normal' ? 'bg-green-50 text-green-600 border-green-100' : ($hospital['blood'] == 'Warning' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-red-50 text-red-600 border-red-100') }}">
                                {{ $hospital['blood'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold border {{ $hospital['compliance'] == 'Normal' ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                {{ $hospital['compliance'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- National Health Analytics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Patient Visits by District</h4>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="w-16 text-xs text-gray-500">Dhaka</span>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: 85%"></div>
                    </div>
                    <span class="text-xs font-bold text-gray-700">45k</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-16 text-xs text-gray-500">Ctg</span>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: 60%"></div>
                    </div>
                    <span class="text-xs font-bold text-gray-700">28k</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-16 text-xs text-gray-500">Sylhet</span>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: 40%"></div>
                    </div>
                    <span class="text-xs font-bold text-gray-700">18k</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Disease Outbreak Alerts</h4>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
                        <span class="text-xs font-bold text-red-800">Dengue Flare-up</span>
                    </div>
                    <span class="text-[10px] text-red-600 font-bold">HIGH RISK</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-600"></div>
                        <span class="text-xs font-bold text-amber-800">Seasonal Flu</span>
                    </div>
                    <span class="text-[10px] text-amber-600 font-bold">MONITORING</span>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Insurance Claims Trend</h4>
            <div class="h-32 flex items-end justify-between gap-1 mt-4">
                @foreach([30, 45, 35, 60, 75, 55, 90] as $h)
                    <div class="w-full bg-indigo-100 hover:bg-indigo-500 transition-colors rounded-t-lg" style="height: {{ $h }}%"></div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 px-1">
                <span class="text-[10px] text-gray-400">MON</span>
                <span class="text-[10px] text-gray-400">SUN</span>
            </div>
        </div>
    </div>
@endsection
