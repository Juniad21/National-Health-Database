<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-cover bg-center relative py-12" style="background-image: url('{{ asset('images/login-bg.png') }}');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 to-green-900/40 backdrop-blur-[2px]"></div>

        <div class="relative w-full max-w-2xl p-4">
            <!-- Glass Card -->
            <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/20 overflow-hidden">
                
                <!-- Brand Header -->
                <div class="bg-gradient-to-r from-[#006a4e] to-[#004d39] p-6 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                    
                    <div class="relative z-10">
                        <img src="{{ asset('images/logo.png') }}" alt="BD Health Logo" class="w-16 h-16 mx-auto rounded-xl shadow-lg mb-2 border-2 border-white/50 bg-white">
                        <h1 class="text-xl font-black text-white tracking-tight uppercase">Registration Portal</h1>
                        <p class="text-green-100 text-[10px] font-bold tracking-[0.2em] mt-1 opacity-80 uppercase">National Health Database of Bangladesh</p>
                    </div>
                </div>

                <div class="p-10">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Role Selection -->
                        <div class="space-y-2">
                            <label for="role" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Account Type') }}</label>
                            <select id="role" name="role"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all font-medium"
                                required autofocus>
                                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient (General Citizen)</option>
                                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor (Medical Professional)</option>
                                <option value="hospital" {{ old('role') == 'hospital' ? 'selected' : '' }}>Hospital / Clinic Entity</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div class="space-y-2 human-field">
                                <label for="first_name" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('First Name') }}</label>
                                <input id="first_name" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="first_name" :value="old('first_name')" />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>

                            <!-- Last Name -->
                            <div class="space-y-2 human-field">
                                <label for="last_name" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Last Name') }}</label>
                                <input id="last_name" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="last_name" :value="old('last_name')" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>
                        </div>

                        <!-- Hospital Name (Only for hospitals) -->
                        <div class="space-y-2 hospital-field" style="display: none;">
                            <label for="hospital_name" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Entity / Hospital Name') }}</label>
                            <input id="hospital_name" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="hospital_name" :value="old('hospital_name')" />
                            <x-input-error :messages="$errors->get('hospital_name')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NID -->
                            <div class="space-y-2 human-field">
                                <label for="nid" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('NID Number') }}</label>
                                <input id="nid" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="nid" :value="old('nid')" />
                                <x-input-error :messages="$errors->get('nid')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>

                            <!-- Age -->
                            <div class="space-y-2 human-field">
                                <label for="age" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Age') }}</label>
                                <input id="age" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="number" name="age" :value="old('age')" />
                                <x-input-error :messages="$errors->get('age')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>
                        </div>

                        <!-- Special Fields (Doctor/Hospital) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2" id="doctor_id_container" style="display: none;">
                                <label for="doctor_id" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Doctor ID / BMDC') }}</label>
                                <input id="doctor_id" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="doctor_id" :value="old('doctor_id')" />
                                <x-input-error :messages="$errors->get('doctor_id')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>

                            <div class="space-y-2" id="dghs_number_container" style="display: none;">
                                <label for="dghs_number" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('DGHS License Number') }}</label>
                                <input id="dghs_number" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="text" name="dghs_number" :value="old('dghs_number')" />
                                <x-input-error :messages="$errors->get('dghs_number')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Email Address') }}</label>
                            <input id="email" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500 font-bold" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div class="space-y-2">
                                <label for="password" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Password') }}</label>
                                <input id="password" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label for="password_confirmation" class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">{{ __('Confirm') }}</label>
                                <input id="password_confirmation" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl text-gray-900 focus:ring-4 focus:ring-green-500/10 focus:border-[#006a4e] transition-all" type="password" name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-500 font-bold" />
                            </div>
                        </div>

                        <div class="pt-4 space-y-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-[#006a4e] to-[#004d39] text-white font-black py-4 rounded-2xl shadow-xl shadow-green-900/20 hover:shadow-green-900/30 hover:-translate-y-0.5 transition-all text-lg uppercase tracking-widest">
                                {{ __('Register Entity') }}
                            </button>
                            
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-[#006a4e] transition-all">
                                    {{ __('Already have an account? Sign In') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const doctorContainer = document.getElementById('doctor_id_container');
            const dghsContainer = document.getElementById('dghs_number_container');
            const humanFields = document.querySelectorAll('.human-field');
            const hospitalFields = document.querySelectorAll('.hospital-field');

            function toggleFields() {
                const role = roleSelect.value;
                doctorContainer.style.display = role === 'doctor' ? 'block' : 'none';
                dghsContainer.style.display = role === 'hospital' ? 'block' : 'none';

                humanFields.forEach(el => el.style.display = (role === 'hospital') ? 'none' : 'block');
                hospitalFields.forEach(el => el.style.display = (role === 'hospital') ? 'block' : 'none');
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-guest-layout>