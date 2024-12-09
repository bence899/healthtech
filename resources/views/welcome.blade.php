<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'HealthTech') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg fixed w-full z-50">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <!-- Updated Healthcare Logo -->
                        <svg class="h-10 w-10 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 8V16M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="ml-3 text-2xl font-bold text-gray-800">HealthTech</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">Login</a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition duration-300">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="pt-24 bg-gradient-to-br from-blue-50 to-white">
            <div class="container mx-auto px-6 py-16">
                <div class="md:flex items-center justify-between">
                    <div class="md:w-1/2 md:pr-12">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                            Professional Healthcare at Your Fingertips
                        </h1>
                        <p class="text-xl text-gray-600 mb-8">
                            Connect with qualified doctors, manage appointments, and access your medical records securely - all from the comfort of your home.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3 rounded-full text-lg font-semibold hover:bg-blue-700 transition duration-300 text-center">
                                Get Started
                            </a>
                            <a href="#services" class="bg-white text-blue-600 px-8 py-3 rounded-full text-lg font-semibold hover:bg-gray-50 transition duration-300 border border-blue-600 text-center">
                                Learn More
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2 mt-12 md:mt-0">
                        <img src="{{ asset('images/doctor-consultation.svg') }}" alt="Online Doctor Consultation" class="w-full">
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="services" class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Our Services</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Online Consultation -->
                    <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300">
                        <div class="text-blue-600 mb-4">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Online Consultation</h3>
                        <p class="text-gray-600">Connect with healthcare professionals from the comfort of your home.</p>
                    </div>

                    <!-- Appointment Booking -->
                    <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300">
                        <div class="text-blue-600 mb-4">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Easy Scheduling</h3>
                        <p class="text-gray-600">Book and manage your appointments with just a few clicks.</p>
                    </div>

                    <!-- Medical Records -->
                    <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300">
                        <div class="text-blue-600 mb-4">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Digital Records</h3>
                        <p class="text-gray-600">Access your medical history and documents securely anytime.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-50 border-t border-gray-100">
            <div class="container mx-auto px-6 py-8">
                <div class="text-center text-gray-600">
                    <p>&copy; {{ date('Y') }} HealthTech. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
