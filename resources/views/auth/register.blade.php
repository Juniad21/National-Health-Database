<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required autofocus>
                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                <option value="hospital" {{ old('role') == 'hospital' ? 'selected' : '' }}>Hospital</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- First Name -->
        <div class="mt-4 human-field">
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name"
                :value="old('first_name')" autocomplete="first_name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4 human-field">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                :value="old('last_name')" autocomplete="last_name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- NID -->
        <div class="mt-4 human-field">
            <x-input-label for="nid" :value="__('NID')" />
            <x-text-input id="nid" class="block mt-1 w-full" type="text" name="nid" :value="old('nid')" />
            <x-input-error :messages="$errors->get('nid')" class="mt-2" />
        </div>

        <!-- Age -->
        <div class="mt-4 human-field">
            <x-input-label for="age" :value="__('Age')" />
            <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" />
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        <!-- Hospital Name -->
        <div class="mt-4 hospital-field" style="display: none;">
            <x-input-label for="hospital_name" :value="__('Hospital Name')" />
            <x-text-input id="hospital_name" class="block mt-1 w-full" type="text" name="hospital_name" :value="old('hospital_name')" />
            <x-input-error :messages="$errors->get('hospital_name')" class="mt-2" />
        </div>

        <!-- Doctor ID -->
        <div class="mt-4" id="doctor_id_container" style="display: none;">
            <x-input-label for="doctor_id" :value="__('Doctor ID')" />
            <x-text-input id="doctor_id" class="block mt-1 w-full" type="text" name="doctor_id"
                :value="old('doctor_id')" />
            <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
        </div>

        <!-- DGHS Number -->
        <div class="mt-4" id="dghs_number_container" style="display: none;">
            <x-input-label for="dghs_number" :value="__('DGHS Number')" />
            <x-text-input id="dghs_number" class="block mt-1 w-full" type="text" name="dghs_number"
                :value="old('dghs_number')" />
            <x-input-error :messages="$errors->get('dghs_number')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

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
            toggleFields(); // Call on load in case of old values
        });
    </script>
</x-guest-layout>