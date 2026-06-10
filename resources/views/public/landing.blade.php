<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic CRM - Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <span class="text-xl font-bold text-blue-600">Clinic CRM</span>
            <div class="flex items-center space-x-4">
                <a href="{{ route('public.booking') }}" class="text-sm text-gray-600 hover:text-blue-600">Book Now</a>
                @auth
                    <a href="{{ route('dashboard.redirect') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="py-20 bg-gradient-to-br from-blue-50 to-white">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Book Your Clinic Appointment</h1>
            <p class="text-lg text-gray-500 mb-8 max-w-2xl mx-auto">
                Easy online booking for aesthetic and clinic services. Choose your service, pick a time, and we'll take care of the rest.
            </p>
            <a href="{{ route('public.booking') }}" class="inline-block bg-blue-600 text-white px-8 py-4 rounded-xl text-lg font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                Book an Appointment
            </a>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-12">How It Works</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">1. Choose Service</h3>
                    <p class="text-gray-500 text-sm">Browse our available treatments and select the service you need.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">2. Pick Date & Time</h3>
                    <p class="text-gray-500 text-sm">Select your preferred date and time slot.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">3. Confirmed!</h3>
                    <p class="text-gray-500 text-sm">You'll receive confirmation and a reminder before your appointment.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-50 border-t border-gray-100 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Clinic CRM. All rights reserved.
        </div>
    </footer>
</body>
</html>
