<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// Check if user is logged in and is a passenger
if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Initialize statistics with default values
$stats = [
    'total_bookings' => 0,
    'total_spent' => 0,
    'upcoming_rides' => 0
];

// Get passenger's statistics
$stats_query = "
    SELECT 
        COUNT(DISTINCT b.id) as total_bookings,
        COALESCE(SUM(b.total_fare), 0) as total_spent,
        COUNT(DISTINCT CASE WHEN b.booking_status = 'confirmed' AND r.ride_date >= CURDATE() THEN b.id END) as upcoming_rides
    FROM bookings b
    LEFT JOIN rides r ON b.ride_id = r.id
    WHERE b.passenger_id = ?";

if ($stmt = $conn->prepare($stats_query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats = $row;
        }
    }
    $stmt->close();
} else {
    error_log("Error preparing statistics query: " . $conn->error);
}

// Initialize upcoming bookings array
$upcoming_bookings = [];

// Get upcoming bookings
$bookings_query = "
    SELECT 
        b.*, 
        r.pickup_location, 
        r.dropoff_location, 
        r.ride_date, 
        r.ride_time,
        u.first_name as driver_name, 
        u.phone as driver_phone
    FROM bookings b
    JOIN rides r ON b.ride_id = r.id
    JOIN users u ON r.driver_id = u.id
    WHERE b.passenger_id = ? 
    AND b.booking_status = 'confirmed' 
    AND r.ride_date >= CURDATE()
    ORDER BY r.ride_date ASC, r.ride_time ASC
    LIMIT 5";

if ($stmt = $conn->prepare($bookings_query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    if ($stmt->execute()) {
        $upcoming_bookings = $stmt->get_result();
    } else {
        error_log("Error executing upcoming bookings query: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("Error preparing upcoming bookings query: " . $conn->error);
}

// Set the page title
$page_title = 'Passenger Dashboard';

// Start output buffering
ob_start();
?>

<div class="min-h-screen bg-gray-100">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Welcome Section with Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h1>
                    <p class="mt-1 text-gray-500">Find and book your next ride</p>
                </div>
                <a href="search_rides.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search Rides
                </a>
            </div>

            <!-- Quick Search Form -->
            <div class="mt-6">
                <form action="search_rides.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="pickup" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                        <input type="text" name="pickup" id="pickup" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="dropoff" class="block text-sm font-medium text-gray-700">Dropoff Location</label>
                        <input type="text" name="dropoff" id="dropoff" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="invisible block text-sm font-medium text-gray-700">Search</label>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Find Rides
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Bookings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_bookings']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Rides -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Upcoming Rides</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['upcoming_rides']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Spent</p>
                        <p class="text-2xl font-semibold text-gray-900">₹<?php echo number_format($stats['total_spent'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Your Upcoming Rides</h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if ($upcoming_bookings && $upcoming_bookings->num_rows > 0): ?>
                    <?php while ($booking = $upcoming_bookings->fetch_assoc()): ?>
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
                                                <?php echo htmlspecialchars($booking['pickup_location']); ?> → 
                                                <?php echo htmlspecialchars($booking['dropoff_location']); ?>
                                            </h3>
                                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <?php echo date('F j, Y', strtotime($booking['ride_date'])); ?> at 
                                                <?php echo date('g:i A', strtotime($booking['ride_time'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Driver</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($booking['driver_name']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Contact</p>
                                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($booking['driver_phone']); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Fare</p>
                                            <p class="mt-1 text-sm text-gray-900">₹<?php echo number_format($booking['total_fare'], 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <a href="view_booking.php?id=<?php echo $booking['id']; ?>" 
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
                        <a href="search_rides.php" class="text-blue-600 hover:text-blue-500">Search for rides</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="my_bookings.php" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">My Bookings</h3>
                        <p class="mt-1 text-sm text-gray-500">View all your ride bookings</p>
                    </div>
                </div>
            </a>

            <a href="favorite_routes.php" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Favorite Routes</h3>
                        <p class="mt-1 text-sm text-gray-500">Manage your favorite routes</p>
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