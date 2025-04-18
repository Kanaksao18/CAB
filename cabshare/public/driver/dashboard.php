<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php'; // Include auth for isLoggedIn/isDriver checks

// Check if user is logged in and is a driver
if (!isLoggedIn() || !isDriver()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Get driver's statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT r.id) as total_rides,
        COUNT(DISTINCT b.id) as total_bookings,
        SUM(b.total_fare) as total_earnings,
        COUNT(DISTINCT CASE WHEN r.status = 'active' THEN r.id END) as active_rides
    FROM rides r
    LEFT JOIN bookings b ON r.id = b.ride_id AND b.booking_status = 'confirmed'
    WHERE r.driver_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get upcoming rides
$stmt = $conn->prepare("
    SELECT r.*, 
           COUNT(DISTINCT b.id) as total_bookings,
           r.available_seats - COALESCE(SUM(b.num_seats), 0) as remaining_seats
    FROM rides r
    LEFT JOIN bookings b ON r.id = b.ride_id AND b.booking_status = 'confirmed'
    WHERE r.driver_id = ? AND r.status = 'active' AND r.ride_date >= CURDATE()
    GROUP BY r.id
    ORDER BY r.ride_date ASC, r.ride_time ASC
    LIMIT 5
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$upcoming_rides = $stmt->get_result();

// Set the page title
$page_title = 'Driver Dashboard';

// Start output buffering
ob_start();
?>

<div class="min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h1>
                    <p class="mt-1 text-gray-500">Manage your rides and track your earnings</p>
                </div>
                <a href="add_ride.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Ride
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Rides -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Rides</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_rides']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Rides -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Rides</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['active_rides']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_bookings']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Earnings</p>
                        <p class="text-2xl font-semibold text-gray-900">₹<?php echo number_format($stats['total_earnings'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Rides -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Upcoming Rides</h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if ($upcoming_rides->num_rows > 0): ?>
                    <?php while ($ride = $upcoming_rides->fetch_assoc()): ?>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <?php echo htmlspecialchars($ride['pickup_location']); ?> → 
                                                <?php echo htmlspecialchars($ride['dropoff_location']); ?>
                                            </h3>
                                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <?php echo date('F j, Y', strtotime($ride['ride_date'])); ?> at 
                                                <?php echo date('g:i A', strtotime($ride['ride_time'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Fare per Seat</p>
                                            <p class="mt-1 text-sm text-gray-900">₹<?php echo number_format($ride['fare_per_seat'], 2); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Available Seats</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo $ride['remaining_seats']; ?> / <?php echo $ride['available_seats']; ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Bookings</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo $ride['total_bookings']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <a href="view_bookings.php?ride_id=<?php echo $ride['id']; ?>" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-500">
                        No upcoming rides. 
                        <a href="add_ride.php" class="text-blue-600 hover:text-blue-500">Add a new ride</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="view_requests.php" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">View Ride Requests</h3>
                        <p class="mt-1 text-sm text-gray-500">Check passenger requests and accept rides</p>
                    </div>
                </div>
            </a>

            <a href="earnings.php" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">View Earnings</h3>
                        <p class="mt-1 text-sm text-gray-500">Track your earnings and analytics</p>
                    </div>
                </div>
            </a>

            <a href="profile.php" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Update Profile</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage your account settings</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout
include '../../includes/components/layout.php';
?> 