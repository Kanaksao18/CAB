<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup = $_POST['pickup_location'];
    $dropoff = $_POST['dropoff_location'];
    $date = $_POST['ride_date'];
    $time = $_POST['ride_time'];
    $seats = $_POST['num_seats'];
    $max_price = $_POST['max_price_per_seat'];
    $min_rating = $_POST['min_driver_rating'];

    $stmt = $conn->prepare("
        INSERT INTO ride_requests (
            passenger_id, 
            pickup_location, 
            dropoff_location, 
            ride_date, 
            ride_time, 
            num_seats, 
            max_price_per_seat, 
            min_driver_rating
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssidd", 
        $_SESSION['user_id'], 
        $pickup, 
        $dropoff, 
        $date, 
        $time, 
        $seats, 
        $max_price, 
        $min_rating
    );

    if ($stmt->execute()) {
        $success = 'Your ride request has been posted successfully!';
    } else {
        $error = 'Failed to post ride request. Please try again.';
    }
}

$page_title = 'Post Ride Request';
ob_start();
?>

<div class="max-w-2xl mx-auto mt-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Post a Ride Request</h2>

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

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Pickup Location</label>
                    <input type="text" name="pickup_location" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Drop-off Location</label>
                    <input type="text" name="dropoff_location" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" name="ride_date" required
                           min="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Time</label>
                    <input type="time" name="ride_time" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Number of Seats</label>
                    <input type="number" name="num_seats" required
                           min="1" max="8"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Maximum Price per Seat</label>
                    <input type="number" name="max_price_per_seat" required
                           min="0" step="0.01"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Minimum Driver Rating</label>
                    <select name="min_driver_rating" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="0">No Preference</option>
                        <option value="3">3+ Stars</option>
                        <option value="4">4+ Stars</option>
                        <option value="4.5">4.5+ Stars</option>
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Post Request
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 