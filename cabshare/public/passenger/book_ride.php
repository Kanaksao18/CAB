<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$ride_id = $_GET['ride_id'] ?? null;
$error = '';
$success = '';

if (!$ride_id) {
    header('Location: dashboard.php');
    exit();
}

// Get ride details
$stmt = $conn->prepare("
    SELECT r.*, 
           u.full_name as driver_name,
           u.phone_number as driver_phone,
           r.available_seats - COALESCE(SUM(b.num_seats), 0) as remaining_seats
    FROM rides r
    JOIN users u ON r.driver_id = u.id
    LEFT JOIN bookings b ON r.id = b.ride_id AND b.booking_status = 'confirmed'
    WHERE r.id = ? AND r.status = 'active'
    GROUP BY r.id
");
$stmt->bind_param("i", $ride_id);
$stmt->execute();
$ride = $stmt->get_result()->fetch_assoc();

if (!$ride) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_seats = $_POST['num_seats'];
    $total_fare = $num_seats * $ride['fare_per_seat'];
    
    try {
        // Create booking record for cash payment
        $stmt = $conn->prepare("
            INSERT INTO bookings (
                ride_id, 
                passenger_id, 
                num_seats, 
                total_fare, 
                payment_method,
                payment_status,
                booking_status
            ) VALUES (?, ?, ?, ?, 'cash', 'pending', 'confirmed')
        ");
        $stmt->bind_param("iiid", 
            $ride_id, 
            $_SESSION['user_id'], 
            $num_seats, 
            $total_fare
        );
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            header('Location: ' . BASE_URL . '/public/passenger/booking_confirmation.php?booking_id=' . $booking_id);
            exit();
        } else {
            $error = 'Failed to create booking';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Set the page title
$page_title = 'Book Ride';

// Start output buffering
ob_start();
?>

<div class="max-w-2xl mx-auto mt-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Book Ride</h2>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-6">
            <h3 class="text-xl font-semibold">
                <?php echo htmlspecialchars($ride['pickup_location']); ?> → 
                <?php echo htmlspecialchars($ride['dropoff_location']); ?>
            </h3>
            <p class="text-gray-600">
                <?php echo date('F j, Y', strtotime($ride['ride_date'])); ?> at 
                <?php echo date('g:i A', strtotime($ride['ride_time'])); ?>
            </p>
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <h4 class="font-semibold text-blue-800">Driver Details</h4>
                <p class="text-blue-700">
                    <span class="font-medium">Name:</span> 
                    <?php echo htmlspecialchars($ride['driver_name']); ?>
                </p>
                <p class="text-blue-700">
                    <span class="font-medium">Phone:</span> 
                    <?php echo htmlspecialchars($ride['driver_phone']); ?>
                </p>
            </div>
            <div class="mt-4">
                <p class="text-lg">
                    <span class="font-semibold">Fare:</span> 
                    ₹<?php echo number_format($ride['fare_per_seat'], 2); ?> per seat
                </p>
                <p class="text-lg">
                    <span class="font-semibold">Available Seats:</span> 
                    <?php echo $ride['remaining_seats']; ?>
                </p>
            </div>
        </div>
        
        <form id="booking-form" method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Number of Seats</label>
                <input type="number" name="num_seats" required 
                       min="1" max="<?php echo $ride['remaining_seats']; ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                       onchange="updateTotal(this.value)">
            </div>
            
            <div class="p-4 bg-gray-50 rounded-lg">
                <label class="block text-gray-700 font-medium mb-2">Total Fare</label>
                <div id="total-fare" class="text-2xl font-bold text-blue-600">₹0.00</div>
                <p class="text-gray-600 mt-2">
                    Payment Method: Cash (Pay to driver)
                </p>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Please note that you'll need to pay the fare in cash to the driver before the ride.
                        </p>
                    </div>
                </div>
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Confirm Booking
            </button>
        </form>
    </div>
</div>

<script>
    function updateTotal(seats) {
        const farePerSeat = <?php echo $ride['fare_per_seat']; ?>;
        const total = (seats * farePerSeat).toFixed(2);
        document.getElementById('total-fare').textContent = '₹' + total;
    }
</script>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 