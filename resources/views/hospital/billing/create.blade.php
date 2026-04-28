@extends('layouts.hospital')

@section('header_title', 'Create New Bill')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="billingForm()">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Bill Details
        </h3>

        <form action="{{ route('hospital.billing.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Patient Selection -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Select Patient <span class="text-red-500">*</span></label>
                    <select name="patient_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }} (NID: {{ $patient->nid }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Issue Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Issued Date <span class="text-red-500">*</span></label>
                    <input type="date" name="issued_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>

            <hr class="border-gray-100 my-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Charges & Fees</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fees Inputs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Fee ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.consultation" name="consultation_fee" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lab Fee ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.lab" name="lab_fee" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Fee ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.medicine" name="medicine_fee" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Fee ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.room" name="room_fee" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Fee ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.emergency" name="emergency_fee" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Charges ($)</label>
                    <input type="number" step="0.01" min="0" x-model="fees.other" name="other_charges" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                </div>
            </div>

            <hr class="border-gray-100 my-6">

            <div class="flex flex-col items-end space-y-4">
                <div class="w-full md:w-1/2">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold text-gray-700">Subtotal:</span>
                        <span class="text-lg font-medium text-gray-800" x-text="'$' + subtotal()"></span>
                    </div>
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                        <span class="text-sm font-bold text-gray-700">Discount ($):</span>
                        <input type="number" step="0.01" min="0" x-model="discount" name="discount" class="w-32 text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" required>
                    </div>
                    <div class="flex justify-between items-center bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <span class="text-lg font-black text-indigo-900">Total Amount:</span>
                        <span class="text-2xl font-black text-indigo-700" x-text="'$' + total()"></span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional notes..."></textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('hospital.billing.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition-colors">Generate Bill</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('billingForm', () => ({
        fees: {
            consultation: 0,
            lab: 0,
            medicine: 0,
            room: 0,
            emergency: 0,
            other: 0
        },
        discount: 0,
        
        subtotal() {
            let sum = parseFloat(this.fees.consultation || 0) + 
                      parseFloat(this.fees.lab || 0) + 
                      parseFloat(this.fees.medicine || 0) + 
                      parseFloat(this.fees.room || 0) + 
                      parseFloat(this.fees.emergency || 0) + 
                      parseFloat(this.fees.other || 0);
            return sum.toFixed(2);
        },
        
        total() {
            let sum = parseFloat(this.subtotal()) - parseFloat(this.discount || 0);
            return Math.max(0, sum).toFixed(2);
        }
    }))
})
</script>
@endsection
