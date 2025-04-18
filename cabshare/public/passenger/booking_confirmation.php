<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    header('Location: dashboard.php');
    exit();
}

// Get booking details
$stmt = $conn->prepare("
    SELECT b.*, 
           r.pickup_location, r.dropoff_location, r.ride_date, r.ride_time,
           u.full_name as driver_name, u.phone_number as driver_phone
    FROM bookings b
    JOIN rides r ON b.ride_id = r.id
    JOIN users u ON r.driver_id = u.id
    WHERE b.id = ? AND b.passenger_id = ?
");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header('Location: dashboard.php');
    exit();
}

// Set the page title
$page_title = 'Booking Confirmation';

// Start output buffering
ob_start();
?>

<div class="max-w-2xl mx-auto mt-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mt-4">Booking Confirmed!</h2>
            <p class="text-gray-600 mt-2">Your ride has been successfully booked.</p>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Please pay ₹<?php echo number_format($booking['total_fare'], 2); ?> in cash to the driver before the ride.
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-b border-gray-200 py-4 my-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">From</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($booking['pickup_location']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">To</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($booking['dropoff_location']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date</p>
                    <p class="font-semibold"><?php echo date('F j, Y', strtotime($booking['ride_date'])); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Time</p>
                    <p class="font-semibold"><?php echo date('g:i A', strtotime($booking['ride_time'])); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Driver Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-blue-600">Name</p>
                    <p class="font-semibold text-blue-900"><?php echo htmlspecialchars($booking['driver_name']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-blue-600">Phone</p>
                    <p class="font-semibold text-blue-900"><?php echo htmlspecialchars($booking['driver_phone']); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Booking Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Booking ID</p>
                    <p class="font-semibold">#<?php echo $booking['id']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number of Seats</p>
                    <p class="font-semibold"><?php echo $booking['num_seats']; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Fare</p>
                    <p class="font-semibold">₹<?php echo number_format($booking['total_fare'], 2); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment Status</p>
                    <p class="font-semibold">Pay by cash to driver</p>
                </div>
            </div>
        </div>

        <div class="text-center space-x-4">
            <a href="dashboard.php" 
               class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Find More Rides
            </a>
            <a href="bookings.php" 
               class="inline-block bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                View My Bookings
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 