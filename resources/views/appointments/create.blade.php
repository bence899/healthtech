<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
                        @csrf

                        <!-- Doctor Selection -->
                        <div>
                            <x-input-label for="doctor_id" :value="__('Select Doctor')" />
                            <select id="doctor_id" name="doctor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
                        </div>

                        <!-- Appointment Date -->
                        <div>
                            <x-input-label for="appointment_date" :value="__('Preferred Date and Time')" />
                            <x-text-input id="appointment_date" 
                                         type="datetime-local"
                                         name="appointment_date"
                                         class="mt-1 block w-full"
                                         :value="old('appointment_date')"
                                         required />
                            <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                        </div>

                        <!-- Reason for Visit -->
                        <div>
                            <x-input-label for="reason_for_visit" :value="__('Reason for Visit')" />
                            <textarea id="reason_for_visit"
                                    name="reason_for_visit"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                    required>{{ old('reason_for_visit') }}</textarea>
                            <x-input-error :messages="$errors->get('reason_for_visit')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button type="button" onclick="window.history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Book Appointment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>