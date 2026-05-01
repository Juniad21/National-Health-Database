@extends('layouts.hospital')

@section('header_title', 'Hospital Blood Bank')

@section('content')
<div class="space-y-8" x-data="{ showStockModal: false, selectedStock: null, showRequestModal: false }">
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Units</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['total_units'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rare Groups</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['rare_units'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Low Stock</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['low_stock'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Out of Stock</p>
                <p class="text-2xl font-black text-gray-800">{{ $stats['out_of_stock'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Stock Management -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        Inventory Control
                    </h3>
                    <div class="flex items-center gap-2">
                        <form class="flex gap-2">
                            <select name="blood_group" onchange="this.form.submit()" class="rounded-xl border-gray-200 bg-white text-[10px] font-black text-gray-500 uppercase tracking-widest py-1.5 px-3">
                                <option value="">All Groups</option>
                                @foreach($bloodGroups as $group)
                                    <option value="{{ $group }}" {{ request('blood_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Group</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Available Units</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Min. Required</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($stocks as $stock)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-black text-sm mx-auto shadow-sm border border-red-100">
                                        {{ $stock->blood_group }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-black text-gray-800">{{ $stock->available_units }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Units</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 bg-{{ $stock->status_color }}-100 text-{{ $stock->status_color }}-600 rounded-full text-[10px] font-black uppercase tracking-tighter border border-{{ $stock->status_color }}-200">
                                        {{ $stock->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-500">{{ $stock->minimum_required_units }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="selectedStock = {{ json_encode($stock) }}; showStockModal = true" class="p-2 bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Request History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-800 text-lg uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Blood Requests
                    </h3>
                    <button @click="showRequestModal = true" class="px-4 py-2 bg-red-600 text-white rounded-xl text-[10px] font-black hover:bg-red-700 transition-all shadow-lg shadow-red-100 uppercase tracking-widest">
                        New Request
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Group</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Units</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Urgency</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Required By</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($requests as $req)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="px-6 py-4 font-black text-red-600">{{ $req->blood_group }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-700">{{ $req->requested_units }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-{{ $req->urgency_color }}-100 text-{{ $req->urgency_color }}-600 rounded text-[9px] font-black uppercase tracking-tighter">
                                        {{ $req->urgency_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-{{ $req->status_color }}-100 text-{{ $req->status_color }}-600 rounded text-[9px] font-black uppercase tracking-tighter">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[10px] font-bold text-gray-400">
                                    {{ $req->required_by ? $req->required_by->format('M d, H:i') : 'No deadline' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($req->status === 'Pending')
                                    <form action="{{ route('hospital.blood_bank.request.cancel', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Cancel Request">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-sm font-black uppercase tracking-widest">No Active Requests</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4 bg-gray-50/50">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Guidance -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-3xl p-8 text-white shadow-xl shadow-red-200 relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-2xl font-black mb-2 tracking-tight">Rare Groups</h4>
                    <p class="text-red-100 text-xs font-bold leading-relaxed mb-6">Tracking rare blood groups (A-, B-, AB-, O-) is critical for regional emergency readiness.</p>
                    <div class="space-y-3">
                        @foreach(App\Models\BloodStock::getRareBloodGroups() as $rareGroup)
                            @php $rareStock = $stocks->where('blood_group', $rareGroup)->first(); @endphp
                            <div class="flex items-center justify-between bg-white/10 rounded-2xl p-3 border border-white/10">
                                <span class="font-black text-sm">{{ $rareGroup }}</span>
                                <span class="text-xs font-bold">{{ $rareStock ? $rareStock->available_units : 0 }} Units</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                <h4 class="text-lg font-black text-gray-800 mb-4 tracking-tight uppercase">Protocol</h4>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">1</div>
                        <p class="text-[11px] text-gray-500 font-bold leading-relaxed">Keep units above the <span class="text-indigo-600">Minimum Required</span> to avoid low-stock alerts on the national registry.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">2</div>
                        <p class="text-[11px] text-gray-500 font-bold leading-relaxed">Requests marked <span class="text-red-600">Critical</span> are prioritized by regional administrators.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">3</div>
                        <p class="text-[11px] text-gray-500 font-bold leading-relaxed">Ensure <span class="text-gray-800">Operational Notes</span> reflect any equipment or storage limitations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div x-show="showStockModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showStockModal" @click="showStockModal = false" class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1">Update Stock</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inventory Modification</p>
                </div>
                <form action="{{ route('hospital.blood_bank.stock.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="blood_group" :value="selectedStock ? selectedStock.blood_group : ''">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Blood Group</label>
                        <div class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black py-3 px-4 text-gray-500" x-text="selectedStock ? selectedStock.blood_group : ''"></div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Available Units</label>
                        <input type="number" name="available_units" :value="selectedStock ? selectedStock.available_units : 0" min="0" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Minimum Buffer Units</label>
                        <input type="number" name="minimum_required_units" :value="selectedStock ? selectedStock.minimum_required_units : 0" min="0" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Storage Notes</label>
                        <textarea name="notes" rows="2" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3" :value="selectedStock ? selectedStock.notes : ''" placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 bg-red-600 text-white font-black rounded-2xl hover:bg-red-700 transition-all shadow-xl shadow-red-100 tracking-widest uppercase text-xs">Commit Inventory Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Request Modal -->
    <div x-show="showRequestModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRequestModal" @click="showRequestModal = false" class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tight leading-none mb-1">New Blood Request</h3>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">External Stock Acquisition</p>
                </div>
                <form action="{{ route('hospital.blood_bank.request.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Blood Group</label>
                            <select name="blood_group" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                                @foreach($bloodGroups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Units Required</label>
                            <input type="number" name="requested_units" value="1" min="1" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Urgency Level</label>
                            <select name="urgency_level" required class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Required By</label>
                            <input type="datetime-local" name="required_by" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Patient (Optional)</label>
                        <select name="patient_id" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3">
                            <option value="">No specific patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->nid }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Request Reason</label>
                        <textarea name="request_reason" rows="2" class="w-full rounded-2xl border-gray-100 bg-gray-50 text-sm font-black focus:ring-red-500 py-3" placeholder="Clinical justification..."></textarea>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 bg-red-600 text-white font-black rounded-2xl hover:bg-red-700 transition-all shadow-xl shadow-red-100 tracking-widest uppercase text-xs">Submit Request To National Hub</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
