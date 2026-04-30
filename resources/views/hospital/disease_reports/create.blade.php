@extends('layouts.hospital')

@section('header_title', 'New Disease Report')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Report Disease Case</h2>
            <p class="text-gray-500 text-sm">Provide accurate data to help national health monitoring and outbreak prevention.</p>
        </div>

        <form action="{{ route('hospital.disease_reports.store') }}" method="POST" class="p-8 space-y-8">
            @csrf

            <!-- Disease Info -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-indigo-500 uppercase tracking-[0.2em]">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Disease Name</label>
                        <input type="text" name="disease_name" value="{{ old('disease_name') }}" 
                               placeholder="e.g. Dengue, Malaria, COVID-19"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 @error('disease_name') border-red-500 @enderror">
                        @error('disease_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">District</label>
                        <input type="text" name="district" value="{{ old('district') }}" 
                               placeholder="e.g. Dhaka, Chittagong, Sylhet"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 @error('district') border-red-500 @enderror">
                        @error('district') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Report Date</label>
                        <input type="date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" 
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 @error('report_date') border-red-500 @enderror">
                        @error('report_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-indigo-500 uppercase tracking-[0.2em]">Case Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Suspected</label>
                        <input type="number" name="suspected_cases" value="{{ old('suspected_cases', 0) }}" min="0"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Confirmed</label>
                        <input type="number" name="confirmed_cases" value="{{ old('confirmed_cases', 0) }}" min="0"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Recovered</label>
                        <input type="number" name="recovered_cases" value="{{ old('recovered_cases', 0) }}" min="0"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Deaths</label>
                        <input type="number" name="death_cases" value="{{ old('death_cases', 0) }}" min="0"
                               class="w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mt-4">
                    <p class="text-xs text-indigo-700 font-medium">
                        <strong>Note:</strong> Severity level will be automatically calculated based on confirmed cases (Low < 20, Medium 20-49, High 50-99, Critical 100+).
                    </p>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-indigo-500 uppercase tracking-[0.2em]">Additional Details</h3>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Notes & Observations</label>
                    <textarea name="notes" rows="4" placeholder="Any specific details, common symptoms, or requested aid..."
                              class="w-full bg-gray-50 border-gray-100 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('hospital.disease_reports.index') }}" class="text-gray-400 font-bold text-sm hover:text-gray-600 transition-colors">Cancel & Return</a>
                <button type="submit" class="bg-indigo-600 text-white px-10 py-3 rounded-2xl font-black shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
