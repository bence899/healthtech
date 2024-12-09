<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ role: 'patient' }">
        @csrf

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" />
            <select id="role" name="role" x-model="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
                <option value="admin">Admin (Demo Only)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Common Fields -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Doctor-specific fields -->
        <div x-show="role === 'doctor'">
            <div class="mt-4">
                <x-input-label for="specialization" :value="__('Specialization')" />
                <x-text-input id="specialization" type="text" name="specialization" :value="old('specialization')" />
                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="qualifications" :value="__('Qualifications')" />
                <x-text-input id="qualifications" type="text" name="qualifications" :value="old('qualifications')" />
                <x-input-error :messages="$errors->get('qualifications')" class="mt-2" />
            </div>
        </div>

        <!-- Patient-specific fields -->
        <div x-show="role === 'patient'">
            <div class="mt-4">
                <x-input-label for="emergency_contact" :value="__('Emergency Contact')" />
                <x-text-input id="emergency_contact" type="tel" name="emergency_contact" :value="old('emergency_contact')" />
                <x-input-error :messages="$errors->get('emergency_contact')" class="mt-2" />
            </div>
        </div>

        <!-- Common Additional Fields -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" type="tel" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
            <x-text-input id="date_of_birth" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>

        <!-- Password Fields -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
