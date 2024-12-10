<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('appointments.create') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg mb-2">Book Appointment</h3>
                    <p class="text-gray-600">Schedule a new appointment with a doctor</p>
                </a>
                <a href="{{ route('documents.index') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg mb-2">Medical Records</h3>
                    <p class="text-gray-600">View and manage your medical documents</p>
                </a>
                <a href="{{ route('profile.edit') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg mb-2">Update Profile</h3>
                    <p class="text-gray-600">Manage your personal information</p>
                </a>
            </div>

            <!-- Upcoming Appointments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Your Upcoming Appointments</h3>
                    <!-- Add appointments list here -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 