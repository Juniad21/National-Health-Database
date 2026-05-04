@extends('layouts.patient')

@section('header_title', 'Bills & Payments')

@section('content')
<div class="space-y-6" x-data="{ 
    paymentModal: false, 
    selectedBill: null,
    paymentMethod: 'card',
    processing: false,
    openPayment(bill) {
        this.selectedBill = bill;
        this.paymentModal = true;
    },
    confirmPayment() {
        this.processing = true;
        $refs.paymentForm.submit();
    }
}">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">My Medical Bills</h2>
    </div>

    @if($bills->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($bills as $bill)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">{{ $bill->hospital->name }}</h4>
                                <p class="text-xs font-mono text-gray-400 mt-1 uppercase tracking-widest">{{ $bill->bill_number }}</p>
                            </div>
                            @if($bill->payment_status == 'paid')
                                <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1 rounded-full uppercase border border-emerald-200">Paid</span>
                            @elseif($bill->payment_status == 'partially_paid')
                                <span class="bg-amber-100 text-amber-700 text-xs font-black px-3 py-1 rounded-full uppercase border border-amber-200">Partially Paid</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs font-black px-3 py-1 rounded-full uppercase border border-red-200">Unpaid</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-2xl p-3 border border-gray-100 text-center">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Amount</p>
                                <p class="text-xl font-black text-gray-800">৳{{ number_format($bill->total_amount, 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-3 border border-gray-100 text-center">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Due Amount</p>
                                <p class="text-xl font-black {{ $bill->due_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">৳{{ number_format($bill->due_amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 border-t border-gray-50 pt-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Issued Date</span>
                                <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($bill->issued_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Paid Amount</span>
                                <span class="font-semibold text-gray-800">৳{{ number_format($bill->paid_amount, 2) }}</span>
                            </div>
                        </div>

                        @if($bill->notes)
                            <div class="mt-4 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 text-xs text-indigo-700 italic">
                                <strong>Note:</strong> {{ $bill->notes }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
                        <button class="text-gray-500 font-bold text-sm hover:text-gray-700 transition-colors">
                            Download Receipt
                        </button>
                        @if($bill->payment_status !== 'paid')
                            <button @click="openPayment({{ json_encode($bill) }})" 
                                class="bg-teal-600 text-white px-6 py-2 rounded-xl font-bold text-sm shadow-md shadow-teal-100 hover:bg-teal-700 transition-all">
                                Pay Now
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-300">
            <svg class="w-20 h-20 mx-auto text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-500">No bills found</h3>
            <p class="text-gray-400 mt-2">You don't have any medical bills recorded yet.</p>
        </div>
    @endif

    <!-- Payment Modal -->
    <div x-show="paymentModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="paymentModal = false">
                <div class="absolute inset-0 bg-gray-900 opacity-60"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-black text-gray-800">Complete Payment</h3>
                        <button @click="paymentModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <template x-if="selectedBill">
                        <div class="space-y-6">
                            <div class="bg-teal-50 rounded-2xl p-5 border border-teal-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-teal-800 font-semibold" x-text="selectedBill.hospital.name"></span>
                                    <span class="text-teal-600 font-black text-xl" x-text="'৳' + parseFloat(selectedBill.due_amount).toFixed(2)"></span>
                                </div>
                                <p class="text-teal-600 text-xs mt-1" x-text="'Bill #' + selectedBill.bill_number"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">Select Payment Method</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'border-teal-500 bg-teal-50' : 'border-gray-200'" class="border-2 rounded-2xl p-4 flex flex-col items-center gap-2 transition-all">
                                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <span class="text-xs font-bold text-gray-700">Card</span>
                                    </button>
                                    <button @click="paymentMethod = 'bkash'" :class="paymentMethod === 'bkash' ? 'border-pink-500 bg-pink-50' : 'border-gray-200'" class="border-2 rounded-2xl p-4 flex flex-col items-center gap-2 transition-all">
                                        <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center text-white font-black text-xs italic">b</div>
                                        <span class="text-xs font-bold text-gray-700">bKash</span>
                                    </button>
                                    <button @click="paymentMethod = 'rocket'" :class="paymentMethod === 'rocket' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'" class="border-2 rounded-2xl p-4 flex flex-col items-center gap-2 transition-all">
                                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-black text-xs italic">R</div>
                                        <span class="text-xs font-bold text-gray-700">Rocket</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="paymentMethod === 'card'" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Card Number</label>
                                    <input type="text" placeholder="**** **** **** ****" class="w-full border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiry</label>
                                        <input type="text" placeholder="MM/YY" class="w-full border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">CVV</label>
                                        <input type="text" placeholder="***" class="w-full border-gray-200 rounded-xl">
                                    </div>
                                </div>
                            </div>

                            <div x-show="paymentMethod !== 'card'" class="space-y-4" style="display: none;">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1" x-text="paymentMethod === 'bkash' ? 'bKash Number' : 'Rocket Number'"></label>
                                    <input type="text" placeholder="017********" class="w-full border-gray-200 rounded-xl">
                                </div>

                            </div>

                            <form x-ref="paymentForm" method="POST" :action="'/patient/bills/' + selectedBill.id + '/pay'" class="space-y-4 pt-4 border-t border-gray-100">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount to Pay</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-lg font-black">৳</span>
                                        </div>
                                        <input type="number" step="0.01" name="payment_amount" :max="selectedBill.due_amount" :value="selectedBill.due_amount" required
                                            class="w-full pl-8 py-3 border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 font-black text-gray-800 text-lg">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2 font-medium">You can pay partially. Any remaining amount will stay as due.</p>
                                </div>

                                <button type="button" @click="confirmPayment()" 
                                    class="w-full bg-teal-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-teal-100 hover:bg-teal-700 transition-all flex items-center justify-center gap-2"
                                    :disabled="processing">
                                    <svg x-show="processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="processing ? 'Processing...' : 'Confirm & Pay Now'"></span>
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
