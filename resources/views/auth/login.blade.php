<x-guest-layout>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-50 to-white">
        <!-- Header -->
        <div class="w-full bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg">
            <div class="max-w-md mx-auto px-4 py-8">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2z"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-white">CareHub</h1>
                </div>
                <p class="text-center text-blue-100 text-sm mt-2 font-medium">National Health Database Access</p>
            </div>
        </div>

        <!-- Login Form -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-lg shadow-xl p-8 space-y-6">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
                        <p class="text-gray-600 text-sm mt-1">Sign in to your CareHub account</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                            <input id="email" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                placeholder="you@example.com"
                                required 
                                autofocus 
                                autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                            <input id="password" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required 
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-600 cursor-pointer" name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-sm text-blue-600 hover:text-blue-700 font-medium transition" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="w-full mt-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition duration-200">
                            {{ __('Sign In') }}
                        </button>
                    </form>
                </div>

                <!-- Footer Info -->
                <div class="mt-6 px-4 py-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-gray-600 text-center">
                        <span class="font-semibold text-gray-700">Secure Login:</span> Your credentials are encrypted and protected.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
