@extends('layouts.hospital')

@section('header_title', 'Insurance Claims Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Billing & Insurance</h2>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('hospital.billing.index') }}" 
                class="{{ request()->routeIs('hospital.billing.index') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Hospital Bills
            </a>
            <a href="{{ route('hospital.billing.claims') }}" 
                class="{{ request()->routeIs('hospital.billing.claims') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Insurance Claims
            </a>
        </nav>
    </div>

    <!-- Filters -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('hospital.billing.claims') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-64">
                <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="settled" {{ request('status') == 'settled' ? 'selected' : '' }}>Settled</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-50 text-indigo-700 font-medium rounded-lg hover:bg-indigo-100 transition-colors shadow-sm text-sm border border-indigo-200">
                    Filter
                </button>
                <a href="{{ route('hospital.billing.claims') }}" class="px-4 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors shadow-sm text-sm border border-gray-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Claims List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                    <th class="p-4 font-bold text-gray-700">Bill Number</th>
                    <th class="p-4 font-bold text-gray-700">Patient</th>
                    <th class="p-4 font-bold text-gray-700">Insurance Provider</th>
                    <th class="p-4 font-bold text-gray-700 text-right">Claimed</th>
                    <th class="p-4 font-bold text-gray-700 text-right">Approved</th>
                    <th class="p-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="p-4 font-bold text-gray-700 text-center">Update Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($claims as $claim)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-mono text-indigo-600 font-medium">
                            <a href="#" class="hover:underline">{{ $claim->bill->bill_number }}</a>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $claim->patient->first_name }} {{ $claim->patient->last_name }}</div>
                            <div class="text-xs text-gray-500">NID: {{ $claim->patient->nid }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $claim->insurance_provider }}</div>
                            <div class="text-xs text-gray-500">Policy: {{ $claim->policy_number }}</div>
                        </td>
                        <td class="p-4 font-bold text-gray-800 text-right">৳{{ number_format($claim->claim_amount, 2) }}</td>
                        <td class="p-4 font-bold text-emerald-600 text-right">
                            {{ $claim->approved_amount ? '৳' . number_format($claim->approved_amount, 2) : '-' }}
                        </td>
                        <td class="p-4 text-center">
                            @if($claim->claim_status == 'approved')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded border border-blue-200">Approved</span>
                            @elseif($claim->claim_status == 'settled')
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded border border-emerald-200">Settled</span>
                            @elseif($claim->claim_status == 'rejected')
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded border border-red-200">Rejected</span>
                            @else
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded border border-amber-200">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-center relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs bg-indigo-50 px-3 py-1.5 rounded border border-indigo-100">
                                Update
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-xl z-20 border border-gray-100 overflow-hidden" style="display: none;">
                                <form action="{{ route('hospital.billing.claim.status.update', $claim->id) }}" method="POST" class="p-3">
                                    @csrf
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                                    <select name="claim_status" class="w-full text-xs border-gray-300 rounded mb-2">
                                        <option value="pending" {{ $claim->claim_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $claim->claim_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $claim->claim_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="settled" {{ $claim->claim_status == 'settled' ? 'selected' : '' }}>Settled</option>
                                    </select>
                                    
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Approved Amount</label>
                                    <input type="number" step="0.01" name="approved_amount" value="{{ $claim->approved_amount }}" placeholder="Leave blank if not approved" class="w-full text-xs border-gray-300 rounded mb-2">
                                    
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full text-xs border-gray-300 rounded mb-2">{{ $claim->remarks }}</textarea>
                                    
                                    <button type="submit" class="w-full bg-indigo-600 text-white text-xs font-bold py-1.5 rounded hover:bg-indigo-700">Save Changes</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No insurance claims found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">
            {{ $claims->links() }}
        </div>
    </div>
</div>
@endsection
