<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $doctor->first_name }} {{ $doctor->last_name }} - Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
                    Back to previous page
                </a>
            </div>

            @if(!$profile)
            <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h1 class="text-2xl font-black text-gray-800 mb-2">Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</h1>
                <p class="text-gray-500 max-w-md mx-auto italic">This doctor has not completed their professional profile yet. Please contact the hospital for more information.</p>
            </div>
            @else
            <!-- MAIN CARD -->
            <div class="bg-white rounded-3xl shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                
                <div class="px-8 pb-12 -mt-16">
                    <div class="flex flex-col md:flex-row gap-8 items-end">
                        <div class="w-40 h-40 rounded-3xl bg-white p-2 shadow-2xl overflow-hidden flex-shrink-0">
                            @if($profile->profile_photo)
                                <img src="{{ asset('storage/' . $profile->profile_photo) }}" alt="Dr. {{ $profile->full_name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <div class="w-full h-full bg-blue-50 flex items-center justify-center text-blue-300 rounded-2xl">
                                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 pb-4">
                            <div class="flex items-center gap-3">
                                <h1 class="text-3xl font-black text-gray-800">Dr. {{ $profile->full_name }}</h1>
                                @if($profile->verification_status == 'Verified')
                                    <div class="group relative">
                                        <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Verified Medical Provider</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-blue-600 font-bold text-lg">{{ $profile->specialization }} &bull; {{ $profile->designation ?? 'Consultant' }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-1">
                                    @php $avgRating = $doctor->reviews_avg_rating ?? 0; @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $avgRating ? 'text-yellow-400 fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-sm font-black text-gray-800">{{ number_format($avgRating, 1) }} <span class="text-gray-400 font-bold">({{ $doctor->reviews_count }} reviews)</span></p>
                            </div>
                            <p class="text-gray-500 mt-1 font-medium">{{ $profile->hospital->name ?? $profile->hospital_name }}</p>
                        </div>
                    </div>

                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-8">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Professional Overview</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 mb-1">Experience</p>
                                        <p class="text-sm font-black text-gray-700">{{ $profile->years_of_experience }} Years +</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 mb-1">Qualifications</p>
                                        <p class="text-sm font-black text-gray-700 leading-tight">{{ $profile->qualifications }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 mb-1">Languages</p>
                                        <p class="text-sm font-black text-gray-700">{{ $profile->languages_spoken ?? 'English, Bengali' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                                <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Consultation Details</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs font-bold text-blue-400 mb-1">Consultation Fee</p>
                                        <p class="text-2xl font-black text-blue-800">৳{{ number_format($profile->consultation_fee, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-blue-400 mb-1">Appointment Type</p>
                                        <p class="text-sm font-black text-blue-700">{{ $profile->consultation_type }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="md:col-span-2 space-y-8">
                            <div>
                                <h3 class="text-lg font-black text-gray-800 mb-3 flex items-center gap-2">
                                    <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                                    About Dr. {{ $profile->full_name }}
                                </h3>
                                <p class="text-gray-600 leading-relaxed italic">
                                    {{ $profile->biography ?: "Dr. {$profile->full_name} is a dedicated medical professional specializing in {$profile->specialization} with over {$profile->years_of_experience} years of clinical experience." }}
                                </p>
                            </div>

                            <div class="pt-4">
                                <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                    Availability & Schedule
                                </h3>
                                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Chamber Days</p>
                                        <div class="flex flex-wrap gap-2">
                                            @php $availDays = explode(', ', $profile->available_days ?? ''); @endphp
                                            @foreach(['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day)
                                                <span class="px-2 py-1 rounded text-[10px] font-bold border {{ in_array($day, $availDays) || in_array($day.'day', $availDays) ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-white text-gray-300 border-gray-100' }}">
                                                    {{ $day }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Time Slots</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $profile->available_time_slots ?: 'Contact Hospital' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-4">
                                <div>
                                    <h3 class="text-sm font-black text-gray-800 mb-3 uppercase tracking-wider">Services</h3>
                                    <ul class="space-y-2">
                                        @foreach(explode(', ', $profile->services_offered ?? '') as $service)
                                            @if($service)
                                            <li class="flex items-center gap-2 text-xs text-gray-600">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                                {{ $service }}
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-800 mb-3 uppercase tracking-wider">Awards</h3>
                                    <ul class="space-y-2">
                                        @foreach(explode(', ', $profile->awards_certifications ?? '') as $award)
                                            @if($award)
                                            <li class="flex items-start gap-2 text-xs text-gray-600">
                                                <svg class="w-4 h-4 text-yellow-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                {{ $award }}
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REVIEWS SECTION -->
            <div class="mt-12">
                <h3 class="text-2xl font-black text-gray-800 mb-8 flex items-center gap-3">
                    <span class="w-2 h-8 bg-yellow-400 rounded-full"></span>
                    Patient Feedback
                </h3>

                <div class="space-y-6">
                    @forelse($reviews as $review)
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start gap-4 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 font-bold text-sm">
                                        {{ substr($review->patient->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">{{ $review->patient->first_name }} {{ substr($review->patient->last_name, 0, 1) }}.</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-gray-600 text-sm leading-relaxed italic">"{{ $review->comment }}"</p>
                            @else
                                <p class="text-gray-300 text-xs italic">Patient left a rating without a comment.</p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-gray-100/50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold">No reviews yet for this doctor.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            <div class="mt-12 text-center text-gray-400 text-xs font-medium">
                <p>&copy; {{ date('Y') }} National Health Database &bull; Verified Professional Directory</p>
            </div>
        </div>
    </div>
</body>
</html>
