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
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                            <div class="text-sm text-red-600">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
                        @csrf

                        <!-- Doctor Selection -->
                        <div class="mb-6">
                            <x-input-label for="doctor_id" :value="__('Select Doctor *')" />
                            <select id="doctor_id" name="doctor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" 
                                            data-schedule='{{ $doctor->working_hours }}'
                                            {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('doctor_id')" class="mt-2" />
                        </div>

                        <!-- Doctor's Schedule Display -->
                        <div id="scheduleDisplay" class="mb-6 hidden">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Available Hours</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="mb-6">
                            <x-input-label for="appointment_date" :value="__('Appointment Date *')" />
                            <input type="date" 
                                   id="appointment_date" 
                                   name="appointment_date" 
                                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                   required 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ old('appointment_date') }}">
                            <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="appointment_time" :value="__('Appointment Time *')" />
                            <select id="appointment_time" 
                                    name="appointment_time" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                <option value="">Select time</option>
                            </select>
                            <x-input-error :messages="$errors->get('appointment_time')" class="mt-2" />
                        </div>

                        <!-- Reason -->
                        <div class="mb-6">
                            <x-input-label for="reason_for_visit" :value="__('Reason for Visit *')" />
                            <textarea id="reason_for_visit" 
                                      name="reason_for_visit" 
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                      required>{{ old('reason_for_visit') }}</textarea>
                            <x-input-error :messages="$errors->get('reason_for_visit')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                            <x-primary-button>Book Appointment</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="timeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Appointment Time</h3>
                <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto" id="timeSlots">
                    <!-- Time slots will be inserted here -->
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="closeTimeModal"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="scheduleDisplay" class="mt-6 hidden">
        <h3 class="text-lg font-medium text-gray-900">Doctor's Schedule</h3>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctor_id');
            const dateInput = document.getElementById('appointment_date');
            let currentSchedule = null;

            // Initialize flatpickr
            const picker = flatpickr("#appointment_date", {
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: [
                    function(date) {
                        // disable weekends
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ],
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        updateTimeSlots(selectedDates[0]);
                    }
                }
            });

            // Doctor selection change handler
            doctorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    try {
                        currentSchedule = JSON.parse(selectedOption.dataset.schedule);
                        displaySchedule(currentSchedule);
                        if (dateInput.value) {
                            updateTimeSlots(picker.selectedDates[0]);
                        }
                    } catch (e) {
                        console.error('Error parsing schedule:', e);
                    }
                } else {
                    document.getElementById('scheduleDisplay').classList.add('hidden');
                }
            });

            function updateTimeSlots(date) {
                if (!currentSchedule || !date) return;

                const timeSelect = document.getElementById('appointment_time');
                timeSelect.innerHTML = '<option value="">Select time</option>';

                // Get day name in lowercase
                const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                const dayName = days[date.getDay()];

                if (!currentSchedule[dayName] || !currentSchedule[dayName][0]) {
                    timeSelect.disabled = true;
                    return;
                }

                const [startTime, endTime] = currentSchedule[dayName][0].split('-');
                const [startHour, startMinute] = startTime.split(':').map(Number);
                const [endHour, endMinute] = endTime.split(':').map(Number);

                let currentTime = new Date(date);
                currentTime.setHours(startHour, startMinute, 0);

                const endDateTime = new Date(date);
                endDateTime.setHours(endHour, endMinute, 0);

                timeSelect.disabled = false;

                while (currentTime < endDateTime) {
                    const timeString = currentTime.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                    const value = currentTime.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                    const option = new Option(timeString, value);
                    timeSelect.add(option);
                    currentTime.setMinutes(currentTime.getMinutes() + 30);
                }
            }

            function displaySchedule(schedule) {
                const scheduleDisplay = document.getElementById('scheduleDisplay');
                if (!scheduleDisplay) return;

                const scheduleContainer = scheduleDisplay.querySelector('.grid');
                if (!scheduleContainer) return;

                scheduleContainer.innerHTML = '';
                
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                days.forEach(day => {
                    const timeSlot = schedule[day] ? schedule[day][0] : 'Not Available';
                    const div = document.createElement('div');
                    div.className = 'p-3 bg-gray-50 rounded-lg';
                    div.innerHTML = `
                        <span class="font-medium capitalize">${day}:</span>
                        <span class="ml-2 ${timeSlot === 'Not Available' ? 'text-red-500' : 'text-green-600'}">
                            ${timeSlot}
                        </span>
                    `;
                    scheduleContainer.appendChild(div);
                });
                scheduleDisplay.classList.remove('hidden');
            }
        });
    </script>
    @endpush

    <div class="mt-4 text-sm text-gray-600">
        * Required fields
    </div>
</x-app-layout>