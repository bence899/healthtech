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

                        <!-- Available Hours -->
                        <div class="mb-6 hidden" id="scheduleDisplay">
                            <x-input-label :value="__('Available Hours')" />
                            <div class="mt-2 text-sm text-gray-600 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            </div>
                        </div>

                        <!-- Appointment Date and Time -->
                        <div class="mb-6">
                            <x-input-label for="appointment_date" :value="__('Preferred Date and Time *')" />
                            <div class="flex gap-4">
                                <div class="flex-1 relative">
                                    <x-text-input id="appointment_date" 
                                        type="text" 
                                        name="appointment_date" 
                                        placeholder="Click to select date"
                                        class="mt-1 block w-full cursor-pointer bg-white pl-3 pr-10" 
                                        readonly
                                        required />
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 mt-1 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <select id="appointment_time" name="appointment_time" 
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                        required disabled>
                                        <option value="">Select Time</option>
                                    </select>
                                </div>
                            </div>
                            <div id="schedule_error" class="mt-2 text-sm text-red-600 hidden"></div>
                        </div>

                        <!-- Reason for Visit -->
                        <div class="mb-6">
                            <x-input-label for="reason_for_visit" :value="__('Reason for Visit *')" />
                            <textarea id="reason_for_visit" name="reason_for_visit" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                    required>{{ old('reason_for_visit') }}</textarea>
                            <x-input-error :messages="$errors->get('reason_for_visit')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                onclick="window.location='{{ route('appointments.index') }}'">
                                Cancel
                            </button>
                            <button type="submit" 
                                id="submitButton"
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Book Appointment
                            </button>
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

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentSchedule = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctor_id');
            const appointmentDate = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('appointment_time');
            const scheduleError = document.getElementById('schedule_error');

            // Initialize flatpickr
            const picker = flatpickr(appointmentDate, {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: [
                    function(date) {
                        if (!currentSchedule) return true;
                        const day = date.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                        return !currentSchedule[day] || !currentSchedule[day][0];
                    },
                    "today"
                ],
                locale: {
                    firstDayOfWeek: 1
                },
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        updateTimeSlots();
                    }
                }
            });

            // Doctor selection change handler
            doctorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const scheduleDisplay = document.getElementById('scheduleDisplay');
                
                if (selectedOption.value) {
                    currentSchedule = JSON.parse(selectedOption.dataset.schedule);
                    displaySchedule(currentSchedule);
                    scheduleDisplay.classList.remove('hidden');
                    picker.setDate(null);
                    picker.redraw();
                    timeSelect.innerHTML = '<option value="">Select Time</option>';
                    timeSelect.disabled = true;
                } else {
                    currentSchedule = null;
                    scheduleDisplay.classList.add('hidden');
                    picker.setDate(null);
                    picker.redraw();
                }
            });

            // Initialize if doctor is pre-selected
            if (doctorSelect.value) {
                const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
                currentSchedule = JSON.parse(selectedOption.dataset.schedule);
                displaySchedule(currentSchedule);
                picker.redraw();
            }

            // Add form submission handler
            const form = document.getElementById('appointmentForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset previous errors
                const errorElements = document.querySelectorAll('.validation-error');
                errorElements.forEach(el => el.remove());
                
                // Validate form
                const doctorId = document.getElementById('doctor_id').value;
                const appointmentDate = document.getElementById('appointment_date').value;
                const appointmentTime = document.getElementById('appointment_time').value;
                const reasonForVisit = document.getElementById('reason_for_visit').value;
                
                let isValid = true;
                
                if (!doctorId) {
                    showFieldError(document.getElementById('doctor_id'), 'Please select a doctor');
                    isValid = false;
                }
                
                if (!appointmentDate) {
                    showFieldError(document.getElementById('appointment_date'), 'Please select a date');
                    isValid = false;
                }
                
                if (!appointmentTime) {
                    showFieldError(document.getElementById('appointment_time'), 'Please select a time');
                    isValid = false;
                }
                
                if (!reasonForVisit.trim()) {
                    showFieldError(document.getElementById('reason_for_visit'), 'Please provide a reason for visit');
                    isValid = false;
                }
                
                if (isValid) {
                    // Combine date and time for the backend
                    const dateTime = new Date(appointmentDate + ' ' + appointmentTime);
                    const formattedDateTime = dateTime.toISOString().slice(0, 19).replace('T', ' ');
                    
                    // Create hidden input for combined datetime
                    let dateTimeInput = document.createElement('input');
                    dateTimeInput.type = 'hidden';
                    dateTimeInput.name = 'appointment_date';
                    dateTimeInput.value = formattedDateTime;
                    form.appendChild(dateTimeInput);
                    
                    // Submit the form
                    form.submit();
                }
            });
        });

        function updateTimeSlots() {
            const timeSelect = document.getElementById('appointment_time');
            const scheduleError = document.getElementById('schedule_error');
            const dateInput = document.getElementById('appointment_date');
            
            // Reset time select
            timeSelect.innerHTML = '<option value="">Select Time</option>';
            timeSelect.disabled = true;
            scheduleError.classList.add('hidden');
            
            if (!dateInput.value || !currentSchedule) return;
            
            const date = new Date(dateInput.value);
            const day = date.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
            
            if (!currentSchedule[day] || !currentSchedule[day][0]) {
                scheduleError.textContent = 'Doctor is not available on this day. Please select another date.';
                scheduleError.classList.remove('hidden');
                return;
            }
            
            const [start, end] = currentSchedule[day][0].split('-');
            const [startHour, startMinute] = start.split(':').map(Number);
            const [endHour, endMinute] = end.split(':').map(Number);
            
            let currentTime = new Date(date);
            currentTime.setHours(startHour, startMinute, 0);
            
            const endTime = new Date(date);
            endTime.setHours(endHour, endMinute, 0);
            
            timeSelect.disabled = false;
            
            while (currentTime <= endTime) {
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
            const scheduleContainer = scheduleDisplay.querySelector('.grid');
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

        function showFieldError(field, message) {
            field.classList.add('border-red-500');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error mt-1 text-sm text-red-600';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    </script>
    @endpush

    <div class="mt-4 text-sm text-gray-600">
        * Required fields
    </div>
</x-app-layout>