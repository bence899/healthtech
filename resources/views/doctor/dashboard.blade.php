<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800">Welcome, Dr. {{ auth()->user()->name }}</h3>
                    <p class="text-gray-600 mt-2">Manage your appointments and patient records from here.</p>
                </div>
            </div>

            <!-- Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Today's Appointments Card -->
                <button onclick="showSection('today')" 
                   class="block bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden shadow-sm sm:rounded-lg 
                          hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    <div class="p-6">
                        <div class="text-3xl font-bold text-blue-600">{{ $todayAppointments->count() }}</div>
                        <div class="text-gray-600">Today's Appointments</div>
                    </div>
                </button>

                <!-- Pending Appointments Card -->
                <button onclick="showSection('pending')"
                   class="block bg-gradient-to-br from-yellow-50 to-yellow-100 overflow-hidden shadow-sm sm:rounded-lg 
                          hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1
                          focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                    <div class="p-6">
                        <div class="text-3xl font-bold text-yellow-600">{{ $pendingAppointments->count() }}</div>
                        <div class="text-gray-600">Pending Appointments</div>
                    </div>
                </button>

                <!-- Total Patients Card -->
                <button onclick="showSection('patients')"
                    class="block bg-gradient-to-br from-green-50 to-green-100 overflow-hidden shadow-sm sm:rounded-lg 
                           hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                    <div class="p-6">
                        <div class="text-3xl font-bold text-green-600">{{ $totalPatients }}</div>
                        <div class="text-gray-600">Total Patients</div>
                    </div>
                </button>
            </div>

            <!-- Dynamic Content Sections (Hidden by default) -->
            <div id="today-section" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg transition-all duration-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300">Today's Appointments</h3>
                    @if($todayAppointments->count() > 0)
                        @include('doctor.partials.appointments-table', ['appointments' => $todayAppointments])
                    @else
                        <p class="text-gray-500 text-center py-8">No appointments scheduled for today</p>
                    @endif
                </div>
            </div>

            <div id="pending-section" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg transition-all duration-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300">Pending Appointments</h3>
                    @if($pendingAppointments->count() > 0)
                        @include('doctor.partials.appointments-table', ['appointments' => $pendingAppointments])
                    @else
                        <p class="text-gray-500 text-center py-8">No pending appointments</p>
                    @endif
                </div>
            </div>

            <!-- Patients Section -->
            <div id="patients-section" class="hidden bg-white overflow-hidden shadow-sm sm:rounded-lg transition-all duration-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 transition-colors duration-300">Your Patients</h3>
                    @if($uniquePatients->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Appointments</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Visit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($uniquePatients as $patient)
                                        <tr>
                                            <td class="px-6 py-4">{{ $patient->user->name }}</td>
                                            <td class="px-6 py-4">{{ $patient->appointments_count }}</td>
                                            <td class="px-6 py-4">
                                                {{ $patient->appointments_max_appointment_date ? Carbon\Carbon::parse($patient->appointments_max_appointment_date)->format('M d, Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No patients found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Decline Appointment</h3>
                <form id="cancelForm" method="POST" class="mt-4">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="action" value="decline">
                    <div class="mt-2">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Reason for declining</label>
                        <textarea
                            name="cancellation_reason"
                            id="cancellation_reason"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        ></textarea>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="hideCancelModal()" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                            Decline Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('[id$="-section"]').forEach(section => {
                section.classList.add('hidden');
            });
            // Show selected section
            document.getElementById(sectionId + '-section').classList.remove('hidden');
        }

        function showCancelModal(appointmentId) {
            const modal = document.getElementById('cancelModal');
            const form = document.getElementById('cancelForm');
            form.action = `/doctor/appointments/${appointmentId}/respond`;
            modal.classList.remove('hidden');
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            document.getElementById('cancellation_reason').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCancelModal();
            }
        });

        // Show today's appointments by default
        document.addEventListener('DOMContentLoaded', function() {
            showSection('today');
        });
    </script>
</x-app-layout> 