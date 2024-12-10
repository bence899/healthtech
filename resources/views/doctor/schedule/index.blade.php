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

    <!-- Add this notification div -->
    <div id="notification" class="hidden fixed top-4 right-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-lg transition-all duration-500 z-50">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="h-6 w-6 text-blue-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div id="notification-message"></div>
        </div>
    </div>

    <!-- Add this script -->
    <script>
    function showNotification(message) {
        const notification = document.getElementById('notification');
        const messageElement = document.getElementById('notification-message');
        messageElement.textContent = message;
        
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }

    // Show notification if there's a success message
    @if(session('success'))
        showNotification("{{ session('success') }}");
    @endif
    </script>
</x-app-layout> 