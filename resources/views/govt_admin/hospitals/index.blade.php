@extends('layouts.govt_admin')

@section('header_title', 'Hospital Capacity & Resource Monitoring')

@section('content')
<div class="space-y-6">
    <!-- Search & Filters -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('govt_admin.hospitals.index') }}" method="GET" class="w-full md:w-1/2 flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or district..." class="flex-1 rounded-2xl border-gray-200 text-sm focus:ring-indigo-500">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-2xl font-bold text-sm hover:bg-indigo-700 transition-colors">Search</button>
        </form>
        <div class="flex gap-4">
            <span class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span> Critical Shortage
            </span>
            <span class="flex items-center gap-2 text-xs font-bold text-gray-500">
                <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> Low Stock
            </span>
        </div>
    </div>

    <!-- Hospital Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($hospitals as $hospital)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6 border-b border-gray-50 flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-black text-gray-800">{{ $hospital->name }}</h3>
                    <p class="text-xs text-gray-400 font-medium">{{ $hospital->address }}</p>
                </div>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded-full tracking-widest border border-indigo-100">
                    {{ str_contains($hospital->name, 'College') ? 'Govt' : 'Private' }}
                </span>
            </div>

            <div class="p-6 grid grid-cols-2 gap-6">
                <!-- Bed Capacity -->
                @php $beds = $hospital->resource_summary['beds']; @endphp
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">General Beds</span>
                        <span class="text-sm font-bold {{ $beds && $beds->total_capacity - $beds->currently_in_use < 5 ? 'text-red-500' : 'text-gray-700' }}">
                            {{ $beds ? ($beds->total_capacity - $beds->currently_in_use) . ' / ' . $beds->total_capacity : 'N/A' }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        @php $bedPerc = $beds ? ($beds->currently_in_use / $beds->total_capacity) * 100 : 0; @endphp
                        <div class="h-full {{ $bedPerc > 90 ? 'bg-red-500' : ($bedPerc > 70 ? 'bg-yellow-400' : 'bg-emerald-500') }}" style="width: {{ $bedPerc }}%"></div>
                    </div>
                </div>

                <!-- ICU Units -->
                @php $icu = $hospital->resource_summary['icu']; @endphp
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ICU Units</span>
                        <span class="text-sm font-bold {{ $icu && $icu->total_capacity - $icu->currently_in_use == 0 ? 'text-red-500' : 'text-gray-700' }}">
                            {{ $icu ? ($icu->total_capacity - $icu->currently_in_use) . ' / ' . $icu->total_capacity : 'N/A' }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        @php $icuPerc = $icu ? ($icu->currently_in_use / $icu->total_capacity) * 100 : 0; @endphp
                        <div class="h-full {{ $icuPerc > 90 ? 'bg-red-500' : ($icuPerc > 70 ? 'bg-yellow-400' : 'bg-emerald-500') }}" style="width: {{ $icuPerc }}%"></div>
                    </div>
                </div>

                <!-- Ventilators -->
                @php $vent = $hospital->resource_summary['vent']; @endphp
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ventilators</span>
                        <span class="text-sm font-bold {{ $vent && $vent->total_capacity - $vent->currently_in_use < 2 ? 'text-red-500' : 'text-gray-700' }}">
                            {{ $vent ? ($vent->total_capacity - $vent->currently_in_use) . ' / ' . $vent->total_capacity : 'N/A' }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        @php $ventPerc = $vent ? ($vent->currently_in_use / $vent->total_capacity) * 100 : 0; @endphp
                        <div class="h-full {{ $ventPerc > 80 ? 'bg-orange-500' : 'bg-blue-500' }}" style="width: {{ $ventPerc }}%"></div>
                    </div>
                </div>

                <!-- Oxygen -->
                @php $oxy = $hospital->resource_summary['oxygen']; @endphp
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Oxygen Supply</span>
                        <span class="text-xs font-black uppercase {{ $oxy && ($oxy->currently_in_use / $oxy->total_capacity) < 0.2 ? 'text-red-500' : 'text-emerald-600' }}">
                            {{ $oxy ? (round(($oxy->currently_in_use / $oxy->total_capacity) * 100)) . '%' : 'N/A' }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        @php $oxyPerc = $oxy ? ($oxy->currently_in_use / $oxy->total_capacity) * 100 : 0; @endphp
                        <div class="h-full {{ $oxyPerc < 20 ? 'bg-red-500 animate-pulse' : 'bg-emerald-500' }}" style="width: {{ $oxyPerc }}%"></div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                <div class="flex gap-4">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Doctors</p>
                        <p class="text-sm font-bold text-gray-700">{{ $hospital->doctors->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Active Emergencies</p>
                        <p class="text-sm font-bold text-red-500">{{ $hospital->emergencies->whereNotIn('status', ['Resolved', 'Cancelled', 'Rejected'])->count() }}</p>
                    </div>
                </div>
                <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Detailed Audit &rarr;</a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $hospitals->links() }}
    </div>
</div>
@endsection
