<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Get user's bookings with ride and driver details
$stmt = $conn->prepare("
    SELECT b.*, 
           r.pickup_location, r.dropoff_location, r.ride_date, r.ride_time,
           u.full_name as driver_name, u.phone_number as driver_phone
    FROM bookings b
    JOIN rides r ON b.ride_id = r.id
    JOIN users u ON r.driver_id = u.id
    WHERE b.passenger_id = ?
    ORDER BY r.ride_date DESC, r.ride_time DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result();

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Check if the booking exists and belongs to the user
    $stmt = $conn->prepare("
        SELECT b.*, r.ride_date, r.ride_time 
        FROM bookings b
        JOIN rides r ON b.ride_id = r.id
        WHERE b.id = ? AND b.passenger_id = ?
    ");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if ($booking) {
        // Check if the ride is in the future (can be cancelled)
        $ride_datetime = strtotime($booking['ride_date'] . ' ' . $booking['ride_time']);
        $current_time = time();
        $hours_difference = ($ride_datetime - $current_time) / 3600;
        
        if ($hours_difference > 1) { // Allow cancellation only if more than 1 hour before ride
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET booking_status = 'cancelled' 
                WHERE id = ? AND passenger_id = ?
            ");
            $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
                exit();
            }
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1');
            exit();
        }
    }
}

$page_title = 'My Bookings';
ob_start();
?>

<div class="max-w-6xl mx-auto mt-8 px-4 mb-12">
    <h2 class="text-2xl font-bold mb-6">My Bookings</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Booking cancelled successfully.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            Cannot cancel booking less than 1 hour before ride time.
        </div>
    <?php endif; ?>

    <div class="grid gap-6">
        <?php if ($bookings->num_rows > 0): ?>
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div class="flex-1">
                            <!-- Ride Details -->
                            <div class="mb-4">
                                <h3 class="text-xl font-semibold">
                                    <?php echo htmlspecialchars($booking['pickup_location']); ?> → 
                                    <?php echo htmlspecialchars($booking['dropoff_location']); ?>
                                </h3>
                                <p class="text-gray-600">
                                    <?php echo date('F j, Y', strtotime($booking['ride_date'])); ?> at 
                                    <?php echo date('g:i A', strtotime($booking['ride_time'])); ?>
                                </p>
                            </div>

                            <!-- Driver Details -->
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-semibold text-blue-800">Driver Details</h4>
                                <p class="text-blue-700">
                                    <span class="font-medium">Name:</span> 
                                    <?php echo htmlspecialchars($booking['driver_name']); ?>
                                </p>
                                <p class="text-blue-700">
                                    <span class="font-medium">Phone:</span> 
                                    <?php echo htmlspecialchars($booking['driver_phone']); ?>
                                </p>
                            </div>

                            <!-- Booking Details -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600">Seats Booked</p>
                                    <p class="font-semibold"><?php echo $booking['num_seats']; ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Total Fare</p>
                                    <p class="font-semibold">₹<?php echo number_format($booking['total_fare'], 2); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Payment Status</p>
                                    <p class="font-semibold capitalize"><?php echo $booking['payment_status']; ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Booking Status</p>
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                        <?php echo $booking['booking_status'] === 'confirmed' ? 
                                            'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst($booking['booking_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 md:mt-0 md:ml-6 flex flex-col justify-center">
                            <?php
                            $ride_datetime = strtotime($booking['ride_date'] . ' ' . $booking['ride_time']);
                            $current_time = time();
                            $hours_difference = ($ride_datetime - $current_time) / 3600;
                            
                            if ($booking['booking_status'] === 'confirmed' && $hours_difference > 1):
                            ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="cancel_booking"
                                            class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                        Cancel Booking
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                You haven't made any bookings yet.
                <a href="dashboard.php" class="text-blue-500 hover:text-blue-600 ml-2">
                    Find a ride
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 