@extends('layouts.hospital')

@section('header_title', 'Billing Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Hospital Bills</h2>
        <a href="{{ route('hospital.billing.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition font-medium text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New Bill
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('hospital.billing.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Patient Name or Bill Number..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="w-full md:w-48">
                <select name="payment_status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-50 text-indigo-700 font-medium rounded-lg hover:bg-indigo-100 transition-colors shadow-sm text-sm border border-indigo-200">
                    Search
                </button>
                <a href="{{ route('hospital.billing.index') }}" class="px-4 py-2 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors shadow-sm text-sm border border-gray-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Bills List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                    <th class="p-4 font-bold text-gray-700">Bill Number</th>
                    <th class="p-4 font-bold text-gray-700">Patient</th>
                    <th class="p-4 font-bold text-gray-700">Date Issued</th>
                    <th class="p-4 font-bold text-gray-700 text-right">Total Amount</th>
                    <th class="p-4 font-bold text-gray-700 text-right">Due Amount</th>
                    <th class="p-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="p-4 font-bold text-gray-700 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-mono text-indigo-600 font-medium">{{ $bill->bill_number }}</td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $bill->patient->first_name }} {{ $bill->patient->last_name }}</div>
                            <div class="text-xs text-gray-500">NID: {{ $bill->patient->nid }}</div>
                        </td>
                        <td class="p-4 text-gray-600">{{ \Carbon\Carbon::parse($bill->issued_date)->format('M d, Y') }}</td>
                        <td class="p-4 font-bold text-gray-800 text-right">${{ number_format($bill->total_amount, 2) }}</td>
                        <td class="p-4 font-bold text-red-600 text-right">${{ number_format($bill->due_amount, 2) }}</td>
                        <td class="p-4 text-center">
                            @if($bill->payment_status == 'paid')
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded border border-emerald-200">Paid</span>
                            @elseif($bill->payment_status == 'partially_paid')
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded border border-amber-200">Partially Paid</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded border border-red-200">Unpaid</span>
                            @endif
                        </td>
                        <td class="p-4 text-center relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-500 hover:text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-20 border border-gray-100 overflow-hidden" style="display: none;">
                                <!-- Form to update payment -->
                                <form action="{{ route('hospital.billing.payment.update', $bill->id) }}" method="POST" class="p-3 bg-gray-50 border-b border-gray-100">
                                    @csrf
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Update Paid Amount</label>
                                    <input type="number" step="0.01" name="paid_amount" value="{{ $bill->paid_amount }}" class="w-full text-xs border-gray-300 rounded mb-2" required>
                                    <button type="submit" class="w-full bg-emerald-500 text-white text-xs font-bold py-1.5 rounded hover:bg-emerald-600">Save Payment</button>
                                </form>
                                <!-- Form to submit claim -->
                                <form action="{{ route('hospital.billing.claim.submit', $bill->id) }}" method="POST" class="p-3">
                                    @csrf
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Submit Insurance Claim</label>
                                    <input type="text" name="insurance_provider" placeholder="Provider" class="w-full text-xs border-gray-300 rounded mb-1" required>
                                    <input type="text" name="policy_number" placeholder="Policy #" class="w-full text-xs border-gray-300 rounded mb-1" required>
                                    <input type="number" step="0.01" name="claim_amount" placeholder="Amount" value="{{ $bill->total_amount }}" class="w-full text-xs border-gray-300 rounded mb-2" required>
                                    <button type="submit" class="w-full bg-blue-500 text-white text-xs font-bold py-1.5 rounded hover:bg-blue-600">Submit Claim</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No bills found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">
            {{ $bills->links() }}
        </div>
    </div>
</div>
@endsection
