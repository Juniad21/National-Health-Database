@extends('layouts.patient')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">My Emergency History</h1>
                <p class="text-gray-600">Review all past and active emergency alerts triggered from your account.</p>
            </div>
            <a href="{{ route('patient.emergency.sos') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-red-100 transition-all">
                TRIGGER NEW SOS
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Case ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($emergencies as $emergency)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">#{{ $emergency->id }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800 font-medium">{{ $emergency->emergency_type }}</p>
                            <p class="text-xs text-red-500 font-bold uppercase">{{ $emergency->severity }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $emergency->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'Sent' => 'bg-gray-100 text-gray-600',
                                    'Accepted' => 'bg-indigo-100 text-indigo-600',
                                    'Resolved' => 'bg-green-100 text-green-600',
                                ];
                                $color = $statusColors[$emergency->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="text-xs font-bold {{ $color }} px-2 py-1 rounded">
                                {{ $emergency->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('patient.emergency.view', $emergency->id) }}" class="text-sm font-bold text-indigo-600 hover:underline">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            You haven't triggered any emergency alerts yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
