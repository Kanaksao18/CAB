<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$page_title = 'About Us';
ob_start();
?>

<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">About CabShare</h1>
            <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
                Making ride-sharing simple, affordable, and sustainable for everyone.
            </p>
        </div>

        <!-- Mission Section -->
        <div class="mt-16 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-3xl font-bold text-gray-900">Our Mission</h2>
                <p class="mt-4 text-lg text-gray-500">
                    At CabShare, we're committed to revolutionizing the way people travel by making ride-sharing accessible, 
                    affordable, and environmentally friendly. Our platform connects drivers with empty seats to passengers 
                    heading in the same direction.
                </p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Feature 1 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="bg-blue-100 rounded-lg p-3 inline-block">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Cost-Effective</h3>
                    <p class="mt-2 text-gray-500">
                        Share travel costs with fellow passengers and make your journeys more affordable.
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="bg-green-100 rounded-lg p-3 inline-block">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Eco-Friendly</h3>
                    <p class="mt-2 text-gray-500">
                        Reduce your carbon footprint by sharing rides and contributing to a greener environment.
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="bg-purple-100 rounded-lg p-3 inline-block">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Community</h3>
                    <p class="mt-2 text-gray-500">
                        Connect with like-minded travelers and build a trusted community of ride-sharers.
                    </p>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-900 text-center">How It Works</h2>
            <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Sign Up</h3>
                    <p class="mt-2 text-gray-500">
                        Create your account as a driver or passenger in just a few minutes.
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-bold text-blue-600">2</span>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Find or Offer Rides</h3>
                    <p class="mt-2 text-gray-500">
                        Search for available rides or offer your own as a driver.
                    </p>
                </div>

                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-xl font-bold text-blue-600">3</span>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Travel Together</h3>
                    <p class="mt-2 text-gray-500">
                        Connect with your ride partner and share your journey safely.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../includes/components/layout.php';
?> 