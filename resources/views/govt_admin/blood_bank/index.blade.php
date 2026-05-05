@extends('layouts.govt_admin')

@section('header_title', 'National Blood Bank & Rare Registry')

@section('content')
<div class="space-y-8" x-data="{ 
    showTransferModal: false, 
    selectedSourceStock: null, 
    showNoteModal: false,
    showMatchModal: false,
    selectedRequest: null,
    matchingHospitals: [],
    isLoadingMatches: false,
    async fetchMatches(requestId, bloodGroup, district, requestingHospitalId, requestedUnits) {
        this.isLoadingMatches = true;
        this.matchingHospitals = [];
        console.log('Fetching matches for:', { requestId, bloodGroup, district, requestingHospitalId, requestedUnits });
        
        try {
            const url = `/api/blood-bank/matches?blood_group=${encodeURIComponent(bloodGroup)}&district=${encodeURIComponent(district)}&exclude_hospital_id=${requestingHospitalId}&requested_units=${requestedUnits}&request_id=${requestId}`;
            const response = await fetch(url);
            const data = await response.json();
            
            console.log('Match data received:', data);
            this.matchingHospitals = data.matches || [];
        } catch (error) {
            console.error('Error fetching matches:', error);
            this.matchingHospitals = [];
        } finally {
            this.isLoadingMatches = false;
        }
    }
}">
    
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Units</p>
            <p class="text-xl font-black text-red-600">{{ number_format($stats['total_units']) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Hospitals</p>
            <p class="text-xl font-black text-gray-800">{{ $stats['reporting_hospitals'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending Req</p>
            <p class="text-xl font-black text-yellow-500">{{ $stats['pending_requests'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Critical</p>
            <p class="text-xl font-black text-red-600">{{ $stats['critical_requests'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rare Available</p>
            <p class="text-xl font-black text-purple-600">{{ $stats['rare_units'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Low Stock Facilities</p>
            <p class="text-xl font-black text-orange-500">{{ $stats['low_stock_hospitals'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stock Gaps</p>
            <p class="text-xl font-black text-red-600">{{ $stats['out_of_stock_groups'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            
            <!-- National Availability Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Global Stock Ledger
                    </h3>
                    <form class="flex gap-2">
                        <input type="text" name="hospital" value="{{ request('hospital') }}" placeholder="Hospital..." class="rounded-xl border-gray-100 bg-white text-[10px] font-black py-1.5 px-3 w-32 focus:ring-red-500">
                        <input type="text" name="district" value="{{ request('district') }}" placeholder="District..." class="rounded-xl border-gray-100 bg-white text-[10px] font-black py-1.5 px-3 w-32 focus:ring-red-500">
                        <select name="blood_group" onchange="this.form.submit()" class="rounded-xl border-gray-100 bg-white text-[10px] font-black py-1.5 px-3 focus:ring-red-500">
                            <option value="">Group</option>
                            @foreach($bloodGroups as $group)
                                <option value="{{ $group }}" {{ request('blood_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="p-2 bg-gray-100 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Facility</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Grp</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Surplus</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Last Update</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($stocks as $stock)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-800">{{ $stock->hospital->name }}</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $stock->district }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-black text-red-600">{{ $stock->blood_group }}</td>
                                <td class="px-6 py-4 text-center font-black text-gray-800">{{ $stock->available_units }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-{{ $stock->status_color }}-100 text-{{ $stock->status_color }}-600 rounded-full text-[9px] font-black uppercase tracking-tighter">
                                        {{ $stock->surplus > 0 ? $stock->surplus . ' Surplus' : $stock->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[10px] font-bold text-gray-400">
                                    {{ $stock->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($stock->surplus > 0)
                                        <button @click="selectedSourceStock = {{ json_encode($stock) }}; selectedSourceStock.hospital_name = '{{ $stock->hospital->name }}'; showTransferModal = true" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100">
                                            Transfer
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $stocks->links() }}
                </div>
            </div>

            <!-- National Request Pipeline -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Regional Request Registry
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Requesting Hospital</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Group</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Urgency</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($requests as $req)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-800">{{ $req->requestingHospital->name }}</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $req->district }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-black text-red-600">{{ $req->blood_group }}</td>
                                <td class="px-6 py-4 text-center font-black text-gray-800">{{ $req->requested_units }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-{{ $req->urgency_color }}-100 text-{{ $req->urgency_color }}-600 rounded text-[9px] font-black uppercase tracking-tighter">
                                        {{ $req->urgency_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-{{ $req->status_color }}-100 text-{{ $req->status_color }}-600 rounded text-[9px] font-black uppercase tracking-tighter">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if(in_array($req->status, ['Pending', 'Under Review']))
                                        <button @click="selectedRequest = {{ json_encode($req) }}; matchingHospitals = []; showMatchModal = true; fetchMatches({{ $req->id }}, '{{ $req->blood_group }}', '{{ $req->district }}', {{ $req->requesting_hospital_id }}, {{ $req->requested_units }})" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                                            Match
                                        </button>
                                        @endif
                                        <button @click="selectedRequest = {{ json_encode($req) }}; showNoteModal = true" class="p-1.5 text-gray-400 hover:text-indigo-600 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if($req->status === 'Approved' || $req->status === 'Partially Approved')
                                        <form action="{{ route('govt_admin.blood_bank.request.status', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Fulfilled">
                                            <button type="submit" class="p-1.5 text-emerald-600 hover:text-emerald-700 transition-all" title="Mark Fulfilled">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>

        <!-- Sidebar / Alerts -->
        <div class="space-y-8">
            <!-- Critical Shortages -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-red-50/50 flex items-center justify-between">
                    <h4 class="text-[10px] font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Supply Alerts
                    </h4>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($lowStockAlerts as $alert)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-black text-gray-800 text-xs">{{ $alert->hospital->name }}</span>
                            <span class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-black text-[10px] border border-red-100">{{ $alert->blood_group }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black uppercase text-{{ $alert->status_color }}-500 tracking-widest">{{ $alert->status }}</span>
                            <span class="text-[10px] font-bold text-gray-400">{{ $alert->available_units }} units</span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No Critical Alerts</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Rare Group Registry -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                <h4 class="text-lg font-black mb-4 uppercase tracking-tight relative z-10">Rare Group Assets</h4>
                <div class="space-y-3 relative z-10">
                    @foreach(App\Models\BloodStock::getRareBloodGroups() as $rareGroup)
                        @php $count = App\Models\BloodStock::where('blood_group', $rareGroup)->sum('available_units'); @endphp
                        <div class="flex items-center justify-between bg-white/10 rounded-xl p-3 border border-white/10">
                            <span class="font-black text-sm">{{ $rareGroup }}</span>
                            <span class="text-xs font-black">{{ $count }} Units</span>
                        </div>
                    @endforeach
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div x-show="showTransferModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTransferModal" @click="showTransferModal = false" class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1">Surplus Transfer</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Manual Stock Reallocation</p>
                </div>

                <div class="mb-6 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Source Facility</p>
                    <p class="text-lg font-black text-emerald-800" x-text="selectedSourceStock?.hospital_name"></p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 bg-white text-emerald-600 rounded-lg text-xs font-black shadow-sm" x-text="selectedSourceStock?.blood_group"></span>
                        <span class="text-xs font-bold text-emerald-700" x-text="selectedSourceStock?.surplus + ' Bags Available to Transfer'"></span>
                    </div>
                </div>

                <form action="{{ route('govt_admin.blood_bank.transfer') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="source_hospital_id" :value="selectedSourceStock?.hospital_id">
                    <input type="hidden" name="blood_group" :value="selectedSourceStock?.blood_group">
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Destination Hospital</label>
                        <select name="destination_hospital_id" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-emerald-500 py-3">
                            <option value="">Select Destination...</option>
                            @foreach($allHospitals as $hosp)
                                <option value="{{ $hosp->id }}" x-show="selectedSourceStock?.hospital_id !== {{ $hosp->id }}">{{ $hosp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Units to Transfer</label>
                        <input type="number" name="transfer_units" required min="1" :max="selectedSourceStock?.surplus" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-emerald-500 py-3" placeholder="Enter number of bags...">
                        <p class="text-[9px] text-gray-400 mt-1 font-bold ml-1">Cannot exceed the source hospital's surplus.</p>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="showTransferModal = false" class="px-6 py-3 bg-gray-50 text-gray-400 rounded-xl text-[10px] font-black hover:bg-gray-100 transition-all uppercase tracking-widest">Cancel</button>
                        <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black hover:bg-emerald-700 transition-all uppercase tracking-widest shadow-lg shadow-emerald-100">Execute Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Match Modal -->
    <div x-show="showMatchModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showMatchModal" @click="showMatchModal = false" class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1">Strategic Allocation</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Stock Matching Engine</p>
                </div>

                <div class="mb-8 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Matching Rule Engine</p>
                    <p class="text-xs font-bold text-indigo-800 leading-relaxed">System is analyzing facilities with <span x-text="selectedRequest?.blood_group" class="font-black text-indigo-600"></span> availability, prioritized by proximity (District: <span x-text="selectedRequest?.district" class="font-black text-indigo-600"></span>) and stock depth.</p>
                </div>

                <div class="max-h-64 overflow-y-auto mb-6 pr-2 custom-scrollbar">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="sticky top-0 bg-white shadow-sm border-b border-gray-100">
                                <th class="pb-3 text-[9px] font-black text-gray-400 uppercase tracking-widest">Hospital & District</th>
                                <th class="pb-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Available</th>
                                <th class="pb-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center">Compatibility</th>
                                <th class="pb-3 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <!-- Loading Spinner -->
                            <template x-if="isLoadingMatches">
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Searching National Registry...</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="match in matchingHospitals" :key="match.id">
                                <tr>
                                    <td class="py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-800" x-text="match.hospital_name"></span>
                                            <span class="text-[9px] font-bold text-gray-400 uppercase" x-text="match.district"></span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="text-sm font-black text-emerald-600" x-text="match.available_units"></span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span :class="match.match_type === 'Full Match' ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-100 text-indigo-600'" class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter" x-text="match.match_type">
                                            Match Type
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <form :action="'/govt-admin/blood-bank/request/' + selectedRequest.id + '/match'" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="matched_hospital_id" :value="match.hospital_id">
                                            <input type="number" name="approved_units" :max="Math.min(selectedRequest.requested_units, match.available_units)" :value="Math.min(selectedRequest.requested_units, match.available_units)" min="1" class="w-16 rounded-lg border-gray-100 bg-gray-50 text-[10px] font-black py-1 px-2 focus:ring-indigo-500 mr-2">
                                            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all">Match</button>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="matchingHospitals.length === 0 && !isLoadingMatches">
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No hospitals currently have available <span x-text="selectedRequest?.blood_group" class="text-red-500"></span> stock.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button @click="showMatchModal = false" class="px-6 py-3 bg-gray-50 text-gray-400 rounded-xl text-[10px] font-black hover:bg-gray-100 transition-all uppercase tracking-widest">Cancel</button>
                    <form :action="'/govt-admin/blood-bank/request/' + selectedRequest.id + '/status'" method="POST" class="inline" x-show="selectedRequest">
                        @csrf
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="px-6 py-3 bg-red-50 text-red-600 rounded-xl text-[10px] font-black hover:bg-red-600 hover:text-white transition-all uppercase tracking-widest">Reject Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Note Modal -->
    <div x-show="showNoteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showNoteModal" @click="showNoteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1">Administrative Note</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Internal Commentary</p>
                </div>
                <form :action="'/govt-admin/blood-bank/request/' + selectedRequest?.id + '/note'" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Internal Note</label>
                        <textarea name="admin_note" rows="4" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-indigo-500 py-3" x-text="selectedRequest?.admin_note || ''" placeholder="Add matching details, rejection reason, or follow-up notes..."></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 tracking-widest uppercase text-xs">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
