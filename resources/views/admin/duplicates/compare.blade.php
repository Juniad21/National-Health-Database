@extends('layouts.admin')

@section('header_title', 'Compare & Merge Records')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.duplicates.index') }}" class="flex items-center text-gray-500 hover:text-gray-800 transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Detection Engine
        </a>
    </div>

    <form action="{{ route('admin.duplicates.merge') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative">
            <!-- Center VS Badge -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 hidden lg:flex items-center justify-center w-16 h-16 bg-white border-4 border-gray-50 rounded-full shadow-lg text-blue-600 font-black italic">
                VS
            </div>

            <!-- Patient 1 -->
            <div class="space-y-6">
                <label class="block cursor-pointer group">
                    <input type="radio" name="keep_id" value="{{ $patient1->id }}" class="hidden peer" required checked>
                    <div class="bg-white rounded-3xl border-2 border-gray-100 p-8 shadow-sm transition-all peer-checked:border-blue-500 peer-checked:shadow-blue-100 peer-checked:ring-4 peer-checked:ring-blue-50 relative">
                        <div class="absolute top-6 right-6 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <div class="bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">Keep this record</div>
                        </div>
                        
                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-20 h-20 rounded-3xl bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-3xl shadow-inner">
                                {{ substr($patient1->first_name, 0, 1) }}{{ substr($patient1->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800">{{ $patient1->first_name }} {{ $patient1->last_name }}</h4>
                                <p class="text-blue-600 font-semibold tracking-tight">Record ID: #{{ $patient1->id }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">National ID</span>
                                <span class="font-mono font-bold text-gray-700">{{ $patient1->nid }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Date of Birth</span>
                                <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($patient1->date_of_birth)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Phone</span>
                                <span class="font-bold text-gray-700">{{ $patient1->phone ?? 'Not set' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Blood Group</span>
                                <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg font-bold">{{ $patient1->blood_group ?? '??' }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest block mb-1">Address</span>
                                <span class="text-sm font-medium text-gray-700">{{ $patient1->address ?? 'No address provided' }}</span>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100 grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-center">
                                <p class="text-3xl font-black text-indigo-600">{{ $patient1->appointments->count() }}</p>
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Appointments</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-100 text-center">
                                <p class="text-3xl font-black text-teal-600">{{ $patient1->medicalRecords->count() }}</p>
                                <p class="text-xs font-bold text-teal-400 uppercase tracking-widest">Medical Records</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="merge_id" x-bind:value="keep_id == {{ $patient1->id }} ? {{ $patient2->id }} : {{ $patient1->id }}">
                </label>
            </div>

            <!-- Patient 2 -->
            <div class="space-y-6">
                <label class="block cursor-pointer group">
                    <input type="radio" name="keep_id" value="{{ $patient2->id }}" class="hidden peer">
                    <div class="bg-white rounded-3xl border-2 border-gray-100 p-8 shadow-sm transition-all peer-checked:border-blue-500 peer-checked:shadow-blue-100 peer-checked:ring-4 peer-checked:ring-blue-50 relative">
                        <div class="absolute top-6 right-6 opacity-0 peer-checked:opacity-100 transition-opacity">
                            <div class="bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">Keep this record</div>
                        </div>

                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-20 h-20 rounded-3xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-3xl shadow-inner">
                                {{ substr($patient2->first_name, 0, 1) }}{{ substr($patient2->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800">{{ $patient2->first_name }} {{ $patient2->last_name }}</h4>
                                <p class="text-indigo-600 font-semibold tracking-tight">Record ID: #{{ $patient2->id }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">National ID</span>
                                <span class="font-mono font-bold text-gray-700">{{ $patient2->nid }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Date of Birth</span>
                                <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($patient2->date_of_birth)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Phone</span>
                                <span class="font-bold text-gray-700">{{ $patient2->phone ?? 'Not set' }}</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Blood Group</span>
                                <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg font-bold">{{ $patient2->blood_group ?? '??' }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest block mb-1">Address</span>
                                <span class="text-sm font-medium text-gray-700">{{ $patient2->address ?? 'No address provided' }}</span>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100 grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-center">
                                <p class="text-3xl font-black text-indigo-600">{{ $patient2->appointments->count() }}</p>
                                <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Appointments</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-100 text-center">
                                <p class="text-3xl font-black text-teal-600">{{ $patient2->medicalRecords->count() }}</p>
                                <p class="text-xs font-bold text-teal-400 uppercase tracking-widest">Medical Records</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Merge Logic Settings -->
        <input type="hidden" name="merge_id" id="merge_id_input" value="{{ $patient2->id }}">

        <div class="mt-12 bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h5 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Merge Policy
            </h5>
            <div class="space-y-4 text-gray-600 text-sm leading-relaxed mb-8">
                <p>1. All <span class="font-bold text-gray-800">appointments, medical records, and vaccinations</span> will be transferred to the "Kept" record.</p>
                <p>2. Any missing data in the "Kept" record (like phone or address) will be filled from the "Merged" record.</p>
                <p>3. The <span class="font-bold text-rose-600 uppercase">merged record and its associated user account will be permanently deleted</span>.</p>
            </div>

            <button type="submit" 
                    onclick="return confirm('CRITICAL ACTION: This will permanently merge these records and delete the secondary account. This action cannot be undone. Proceed?')"
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-black text-lg rounded-2xl hover:from-blue-700 hover:to-indigo-800 transition-all shadow-xl shadow-blue-200 uppercase tracking-widest">
                Execute Permanent Merge
            </button>
        </div>
    </form>
</div>

<script>
    // Simple script to handle merge_id logic based on radio selection
    document.querySelectorAll('input[name="keep_id"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const keepId = e.target.value;
            const p1Id = "{{ $patient1->id }}";
            const p2Id = "{{ $patient2->id }}";
            
            document.getElementById('merge_id_input').value = (keepId == p1Id) ? p2Id : p1Id;
        });
    });
</script>
@endsection
