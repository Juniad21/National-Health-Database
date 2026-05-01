@extends('layouts.govt_admin')

@section('header_title', 'Doctor Verification & Management')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('govt_admin.doctors.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Specialization</label>
                <input type="text" name="specialization" value="{{ request('specialization') }}" class="w-full rounded-xl border-gray-200 text-sm" placeholder="e.g. Cardiology">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Hospital</label>
                <input type="text" name="hospital" value="{{ request('hospital') }}" class="w-full rounded-xl border-gray-200 text-sm" placeholder="Search Hospital">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-200 text-sm">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Needs Review" {{ request('status') == 'Needs Review' ? 'selected' : '' }}>Needs Review</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-800 text-white font-bold py-2 rounded-xl text-sm hover:bg-gray-900 transition-colors">Filter</button>
                <a href="{{ route('govt_admin.doctors.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors flex items-center justify-center">Reset</a>
            </div>
        </form>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Doctor</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">BMDC License</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Specialization</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Hospital</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($doctors as $profile)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    @if($profile->profile_photo)
                                        <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="" class="w-full h-full object-cover rounded-xl">
                                    @else
                                        {{ substr($profile->full_name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Dr. {{ $profile->full_name }}</p>
                                    <p class="text-[10px] text-gray-400 italic">Registered: {{ $profile->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 rounded text-[11px] font-mono font-bold text-gray-600">{{ $profile->license_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 font-medium">{{ $profile->specialization }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $profile->hospital->name ?? $profile->hospital_name ?? 'Private Practice' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'Pending' => 'bg-yellow-100 text-yellow-700',
                                    'Verified' => 'bg-green-100 text-green-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    'Needs Review' => 'bg-orange-100 text-orange-700',
                                ];
                                $color = $statusColors[$profile->verification_status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $profile->verification_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('govt_admin.doctors.show', $profile->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-xs font-bold transition-colors">
                                View Profile
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <p class="font-medium italic">No doctors found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($doctors->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $doctors->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
