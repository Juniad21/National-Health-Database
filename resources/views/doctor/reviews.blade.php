@extends('layouts.doctor')

@section('header_title', 'Advanced Feedback Analytics')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    {{-- High-End Analytics Header --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Primary Metric: Average Rating --}}
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-3xl shadow-xl shadow-gray-200/50 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/5 blur-2xl group-hover:bg-yellow-400/10 transition-colors duration-500"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex justify-between items-start">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-widest">Global Rating</p>
                    <div class="p-2 bg-white/10 rounded-xl backdrop-blur-md">
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-8 flex items-baseline gap-2">
                    <h3 class="text-6xl font-black">{{ number_format($averageRating, 1) }}</h3>
                    <span class="text-xl text-gray-500 font-bold">/ 5.0</span>
                </div>
                <p class="mt-2 text-sm text-gray-400">Based on <span class="text-white font-bold">{{ $totalReviews }}</span> verified patient reviews</p>
            </div>
        </div>

        {{-- Distribution Chart --}}
        <div class="bg-white p-8 rounded-3xl shadow-lg shadow-gray-100/50 border border-gray-50 lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Rating Distribution</h3>
            <div class="space-y-4">
                @foreach([5, 4, 3, 2, 1] as $stars)
                <div class="flex items-center gap-4 group">
                    <div class="flex items-center gap-1 w-16 text-sm font-bold text-gray-600">
                        {{ $stars }} <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    </div>
                    <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $stars >= 4 ? 'bg-teal-500' : ($stars == 3 ? 'bg-yellow-500' : 'bg-rose-500') }} rounded-full transition-all duration-1000 ease-out" 
                             style="width: {{ $distributionPercentages[$stars] }}%"></div>
                    </div>
                    <div class="w-12 text-right text-sm font-bold text-gray-400 group-hover:text-gray-800 transition-colors">
                        {{ $distributionPercentages[$stars] }}%
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Detailed Review Feed --}}
    <div>
        <div class="flex justify-between items-center mb-6 px-2">
            <h3 class="text-xl font-black text-gray-800">Patient Testimonials</h3>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ $positivePercentage }}% Positive Satisfaction
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($reviews as $review)
                <div class="bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl hover:shadow-gray-200/40 border border-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                    
                    {{-- Reviewer Info & Rating --}}
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100 flex items-center justify-center text-teal-700 font-black text-xl shadow-inner border border-teal-200/50">
                                    {{ substr($review->patient->first_name, 0, 1) }}
                                </div>
                                @if($review->rating >= 4)
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-lg">{{ $review->patient->first_name }} {{ $review->patient->last_name }}</h4>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 p-2 bg-gray-50 rounded-xl border border-gray-100">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>

                    {{-- Tags --}}
                    @if($review->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($review->tags as $tag)
                                <span class="px-3 py-1 bg-gray-50 border border-gray-200 text-gray-600 text-xs font-bold rounded-lg tracking-wide">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Comment Body --}}
                    @if($review->comment)
                        <div class="relative">
                            <svg class="absolute -top-2 -left-2 w-8 h-8 text-gray-100 -z-10 transform -rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017V14H17.017C15.9124 14 15.017 13.1046 15.017 12V10C15.017 8.89543 15.9124 8 17.017 8H21.017V21H14.017ZM3.017 21L3.017 18C3.017 16.8954 3.91243 16 5.017 16H8.017V14H6.017C4.91243 14 4.017 13.1046 4.017 12V10C4.017 8.89543 4.91243 8 6.017 8H10.017V21H3.017Z" /></svg>
                            <p class="text-gray-600 leading-relaxed font-medium z-10 relative">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic font-medium">No detailed comments provided for this visit.</p>
                    @endif

                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-gray-50/50 rounded-3xl border-2 border-dashed border-gray-200">
                    <div class="w-24 h-24 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 mb-2">No Feedback Yet</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Once patients complete their consultations with you, their ratings and reviews will appear right here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
@endsection
