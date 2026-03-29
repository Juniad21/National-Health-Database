@extends('layouts.patient')

@section('header_title', 'Access Control')

@section('content')

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Manage Doctor Access</h2>
            <p class="text-gray-500 mt-2 text-sm">Control which doctors can view your medical history, prescriptions, and
                lab results.</p>
        </div>

        @if($accessRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 text-sm font-semibold text-gray-600 rounded-tl-xl">Doctor</th>
                            <th class="p-4 text-sm font-semibold text-gray-600">Specialty</th>
                            <th class="p-4 text-sm font-semibold text-gray-600">Status</th>
                            <th class="p-4 text-sm font-semibold text-gray-600 text-right rounded-tr-xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($accessRequests as $req)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold border border-teal-200">
                                            {{ substr($req->doctor->first_name, 0, 1) }}{{ substr($req->doctor->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">Dr. {{ $req->doctor->first_name }}
                                                {{ $req->doctor->last_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm font-medium text-teal-700">{{ $req->doctor->specialty ?? 'General' }}</td>
                                <td class="p-4 text-sm">
                                    @if($req->status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">Approved</span>
                                    @elseif($req->status === 'pending')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200">Pending Request</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200">Revoked</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($req->status === 'pending')
                                            <form method="POST" action="{{ route('patient.access_requests.approve', $req->id) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('patient.access_requests.reject', $req->id) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-1.5 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-sm font-medium rounded-lg shadow-sm transition-colors">Reject</button>
                                            </form>
                                        @elseif($req->status === 'approved')
                                            <form method="POST" action="{{ route('patient.access_requests.reject', $req->id) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">Revoke Access</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('patient.access_requests.approve', $req->id) }}">
                                                @csrf
                                                <button type="submit" class="px-4 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">Restore Access</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 border border-dashed border-gray-300 rounded-2xl bg-gray-50">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-1">No Access Requests</h3>
                <p class="text-gray-500 text-sm">When a doctor requests access to your data, it will appear here.</p>
            </div>
        @endif
    </div>

@endsection