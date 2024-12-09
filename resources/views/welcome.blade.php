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
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-[url('/images/grid.svg')] bg-center [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]"></div>
            </div>
            
            <div class="relative pt-24 pb-16 sm:pt-32 sm:pb-24">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                        <div class="lg:col-span-6 max-w-2xl">
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                                Healthcare Made <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">Simple</span>
                            </h1>
                            <p class="mt-6 text-lg leading-8 text-gray-600">
                                Connect with top healthcare professionals, schedule appointments, and manage your health journey - all in one secure platform.
                            </p>
                            <div class="mt-10 flex items-center gap-x-6">
                                <a href="{{ route('register') }}" 
                                   class="rounded-full bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-4 text-lg font-semibold text-white shadow-sm hover:from-blue-500 hover:to-cyan-400 transition-all duration-300">
                                    Get Started
                                </a>
                                <a href="#features" 
                                   class="rounded-full px-8 py-4 text-lg font-semibold text-blue-600 border-2 border-blue-600 hover:bg-blue-50 transition-all duration-300">
                                    Learn More
                                </a>
                            </div>
                        </div>
                        <div class="lg:col-span-6 mt-16 sm:mt-24 lg:mt-0">
                            <x-application-logo class="w-full h-auto text-blue-600 animate-float" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-white sm:py-32">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-blue-600">Why Choose Us</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Everything you need for better healthcare
                    </p>
                </div>
                <!-- Add feature cards here -->
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
