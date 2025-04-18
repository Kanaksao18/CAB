<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isDriver()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Get available ride requests
$stmt = $conn->prepare("
    SELECT r.*, 
           u.full_name as passenger_name,
           u.phone_number as passenger_phone
    FROM ride_requests r
    JOIN users u ON r.passenger_id = u.id
    WHERE r.status = 'pending'
    AND r.ride_date >= CURDATE()
    ORDER BY r.ride_date ASC, r.ride_time ASC
");
$stmt->execute();
$requests = $stmt->get_result();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $fare = $_POST['fare_per_seat'];
    $available_seats = $_POST['available_seats'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Create the ride
        $stmt = $conn->prepare("
            INSERT INTO rides (
                driver_id, 
                request_id,
                pickup_location, 
                dropoff_location, 
                ride_date, 
                ride_time, 
                available_seats, 
                fare_per_seat
            )
            SELECT ?, ?, pickup_location, dropoff_location, ride_date, ride_time, ?, ?
            FROM ride_requests
            WHERE id = ?
        ");
        $stmt->bind_param("iiidi", 
            $_SESSION['user_id'], 
            $request_id,
            $available_seats,
            $fare,
            $request_id
        );
        $stmt->execute();

        // Update request status
        $stmt = $conn->prepare("
            UPDATE ride_requests 
            SET status = 'accepted' 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        $conn->commit();
        $success = 'You have successfully accepted the ride request!';
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Failed to accept request. Please try again.';
    }
}

$page_title = 'Available Ride Requests';
ob_start();
?>

<div class="max-w-6xl mx-auto mt-8 px-4">
    <h2 class="text-2xl font-bold mb-6">Available Ride Requests</h2>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-6">
        <?php if ($requests->num_rows > 0): ?>
            <?php while ($request = $requests->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold">
                                <?php echo htmlspecialchars($request['pickup_location']); ?> → 
                                <?php echo htmlspecialchars($request['dropoff_location']); ?>
                            </h3>
                            <p class="text-gray-600">
                                <?php echo date('F j, Y', strtotime($request['ride_date'])); ?> at 
                                <?php echo date('g:i A', strtotime($request['ride_time'])); ?>
                            </p>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="font-medium">Passenger Details:</p>
                                    <p><?php echo htmlspecialchars($request['passenger_name']); ?></p>
                                    <p><?php echo htmlspecialchars($request['passenger_phone']); ?></p>
                                </div>
                                <div>
                                    <p>
                                        <span class="font-medium">Seats needed:</span> 
                                        <?php echo $request['num_seats']; ?>
                                    </p>
                                    <p>
                                        <span class="font-medium">Maximum price:</span> 
                                        ₹<?php echo number_format($request['max_price_per_seat'], 2); ?> per seat
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <button onclick="showAcceptForm('<?php echo $request['id']; ?>')"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Accept Request
                            </button>
                        </div>
                    </div>

                    <!-- Accept Form (Hidden by default) -->
                    <div id="accept-form-<?php echo $request['id']; ?>" class="hidden mt-4 pt-4 border-t">
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Your Fare per Seat</label>
                                    <input type="number" name="fare_per_seat" required
                                           min="0" step="0.01" max="<?php echo $request['max_price_per_seat']; ?>"
                                           class="w-full px-4 py-2 border rounded-lg">
                                    <p class="text-sm text-gray-500 mt-1">
                                        Maximum allowed: ₹<?php echo number_format($request['max_price_per_seat'], 2); ?>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Available Seats</label>
                                    <input type="number" name="available_seats" required
                                           min="<?php echo $request['num_seats']; ?>" max="8"
                                           class="w-full px-4 py-2 border rounded-lg">
                                    <p class="text-sm text-gray-500 mt-1">
                                        Minimum required: <?php echo $request['num_seats']; ?> seats
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button type="button" 
                                        onclick="hideAcceptForm('<?php echo $request['id']; ?>')"
                                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                No ride requests available at the moment.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showAcceptForm(requestId) {
    document.getElementById(`accept-form-${requestId}`).classList.remove('hidden');
}

function hideAcceptForm(requestId) {
    document.getElementById(`accept-form-${requestId}`).classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 