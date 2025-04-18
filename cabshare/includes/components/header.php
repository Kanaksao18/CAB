<header class="bg-white shadow-sm">
    <nav class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold">CabShare</a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="<?php echo BASE_URL; ?>" 
                   class="text-gray-700 hover:text-gray-900">Home</a>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (isDriver()): ?>
                        <a href="<?php echo BASE_URL; ?>/public/driver/dashboard.php" 
                           class="text-gray-700 hover:text-gray-900">Offer Ride</a>
                        <a href="<?php echo BASE_URL; ?>/public/driver/my_rides.php" 
                           class="text-gray-700 hover:text-gray-900">My Rides</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/public/passenger/dashboard.php" 
                           class="text-gray-700 hover:text-gray-900">Find Ride</a>
                        <a href="<?php echo BASE_URL; ?>/public/passenger/my_rides.php" 
                           class="text-gray-700 hover:text-gray-900">My Rides</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/public/profile.php" 
                       class="text-gray-700 hover:text-gray-900">Profile</a>
                <?php endif; ?>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="<?php echo BASE_URL; ?>/public/search.php" method="GET" class="w-full">
                    <input type="text" name="q" placeholder="Search rides..." 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </form>
            </div>

            <!-- Auth Buttons / User Menu -->
            <div class="flex items-center space-x-4">
                <?php if (isLoggedIn()): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2">
                            <span>Hello, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2">
                            <a href="<?php echo BASE_URL; ?>/public/profile.php" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="<?php echo BASE_URL; ?>/public/settings.php" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Settings</a>
                            <hr class="my-2">
                            <a href="<?php echo BASE_URL; ?>/public/logout.php" 
                               class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/public/login.php" 
                       class="text-gray-700 hover:text-gray-900">Login</a>
                    <a href="<?php echo BASE_URL; ?>/public/register.php" 
                       class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-700 hover:text-gray-900">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header> 