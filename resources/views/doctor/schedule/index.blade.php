<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('doctor.schedule.store') }}">
                        @csrf
                        
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2 capitalize">
                                    {{ $day }}
                                </label>
                                <input type="text" 
                                       name="working_hours[{{ $day }}][]" 
                                       value="{{ $schedule[$day][0] ?? '09:00-17:00' }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                       pattern="\d{2}:\d{2}-\d{2}:\d{2}"
                                       placeholder="09:00-17:00">
                                @error("working_hours.{$day}")
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 