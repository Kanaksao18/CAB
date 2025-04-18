<?php
if (!isset($BASE_URL)) {
    require_once dirname(dirname(__FILE__)) . '/config.php';
}

// Get the current page name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['user_role'] ?? '';
?>

<nav class="bg-white shadow-lg">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div>
                <a href="<?php echo BASE_URL; ?>/public/<?php echo $user_role; ?>/dashboard.php" 
                   class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-blue-600">CabShare</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-6">
                <?php if (isLoggedIn()): ?>
                    <?php if (isDriver()): ?>
                        <!-- Driver Navigation -->
                        <a href="<?php echo BASE_URL; ?>/public/driver/dashboard.php" 
                           class="<?php echo $current_page === 'dashboard.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            Dashboard
                        </a>
                        <a href="<?php echo BASE_URL; ?>/public/driver/add_ride.php" 
                           class="<?php echo $current_page === 'add_ride.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            Add Ride
                        </a>
                        <a href="<?php echo BASE_URL; ?>/public/driver/profile.php" 
                           class="<?php echo $current_page === 'profile.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            Profile
                        </a>
                    <?php elseif (isPassenger()): ?>
                        <!-- Passenger Navigation -->
                        <a href="<?php echo BASE_URL; ?>/public/passenger/dashboard.php" 
                           class="<?php echo $current_page === 'dashboard.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            Find Rides
                        </a>
                        <a href="<?php echo BASE_URL; ?>/public/passenger/bookings.php" 
                           class="<?php echo $current_page === 'bookings.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            My Bookings
                        </a>
                        <a href="<?php echo BASE_URL; ?>/public/passenger/profile.php" 
                           class="<?php echo $current_page === 'profile.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                            Profile
                        </a>
                    <?php endif; ?>
                    
                    <!-- Logout Button -->
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" 
                       class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Logout
                    </a>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <a href="<?php echo BASE_URL; ?>/public/login.php" 
                       class="<?php echo $current_page === 'login.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                        Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/register.php" 
                       class="<?php echo $current_page === 'register.php' ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600'; ?>">
                        Register
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden pb-4">
            <?php if (isLoggedIn()): ?>
                <?php if (isDriver()): ?>
                    <a href="<?php echo BASE_URL; ?>/public/driver/dashboard.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/public/driver/add_ride.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">Add Ride</a>
                    <a href="<?php echo BASE_URL; ?>/public/driver/profile.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">Profile</a>
                <?php elseif (isPassenger()): ?>
                    <a href="<?php echo BASE_URL; ?>/public/passenger/dashboard.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">Find Rides</a>
                    <a href="<?php echo BASE_URL; ?>/public/passenger/bookings.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">My Bookings</a>
                    <a href="<?php echo BASE_URL; ?>/public/passenger/profile.php" 
                       class="block py-2 text-gray-600 hover:text-blue-600">Profile</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/public/logout.php" 
                   class="block py-2 text-red-600 hover:text-red-800">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/public/login.php" 
                   class="block py-2 text-gray-600 hover:text-blue-600">Login</a>
                <a href="<?php echo BASE_URL; ?>/public/register.php" 
                   class="block py-2 text-gray-600 hover:text-blue-600">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script> 