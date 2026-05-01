@extends('layouts.doctor')

@section('header_title', 'Patient Feedback & Ratings')

@section('content')
<div class="space-y-6">
    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
            <div class="w-16 h-16 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-500">
                <svg class="w-10 h-10 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Average Rating</p>
                <h3 class="text-3xl font-black text-gray-800">{{ number_format($averageRating, 1) }} <span class="text-lg text-gray-400">/ 5.0</span></h3>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
            <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-500">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Reviews</p>
                <h3 class="text-3xl font-black text-gray-800">{{ $reviews->count() }}</h3>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
            <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Positive Feedback</p>
                <h3 class="text-3xl font-black text-gray-800">{{ round(($reviews->where('rating', '>=', 4)->count() / max($reviews->count(), 1)) * 100) }}%</h3>
            </div>
        </div>
    </div>

    {{-- Reviews List --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-black text-gray-800">Recent Reviews</h3>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($reviews as $review)
                <div class="p-8 hover:bg-gray-50/50 transition-all group">
                    <div class="flex flex-col md:flex-row justify-between gap-4 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-lg">
                                {{ substr($review->patient->first_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-gray-800">{{ $review->patient->first_name }} {{ $review->patient->last_name }}</p>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 italic text-gray-600 relative">
                            <svg class="w-8 h-8 text-gray-200 absolute top-2 right-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017V14H17.017C15.9124 14 15.017 13.1046 15.017 12V10C15.017 8.89543 15.9124 8 17.017 8H21.017V21H14.017ZM3.017 21L3.017 18C3.017 16.8954 3.91243 16 5.017 16H8.017V14H6.017C4.91243 14 4.017 13.1046 4.017 12V10C4.017 8.89543 4.91243 8 6.017 8H10.017V21H3.017Z" /></svg>
                            "{{ $review->comment }}"
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic">No written comment provided.</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-gray-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <p class="text-gray-500 font-black">No feedback received yet.</p>
                    <p class="text-gray-400 text-sm">Patients will be able to rate you after completed consultations.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
