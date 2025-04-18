<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isDriver()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $required_fields = ['pickup_location', 'dropoff_location', 'ride_date', 'ride_time', 
                          'available_seats', 'fare_per_seat'];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // Validate date and time
        $ride_datetime = strtotime($_POST['ride_date'] . ' ' . $_POST['ride_time']);
        if ($ride_datetime < time()) {
            throw new Exception("Ride date and time must be in the future");
        }

        // Validate numeric inputs
        if (!is_numeric($_POST['available_seats']) || $_POST['available_seats'] < 1) {
            throw new Exception("Available seats must be at least 1");
        }
        if (!is_numeric($_POST['fare_per_seat']) || $_POST['fare_per_seat'] <= 0) {
            throw new Exception("Fare must be greater than 0");
        }

        // Insert the ride
        $stmt = $conn->prepare("
            INSERT INTO rides (
                driver_id, 
                pickup_location, 
                dropoff_location, 
                ride_date, 
                ride_time, 
                available_seats, 
                fare_per_seat,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
        ");

        $stmt->bind_param(
            "issssid",
            $_SESSION['user_id'],
            $_POST['pickup_location'],
            $_POST['dropoff_location'],
            $_POST['ride_date'],
            $_POST['ride_time'],
            $_POST['available_seats'],
            $_POST['fare_per_seat']
        );

        if ($stmt->execute()) {
            $success_message = "Ride added successfully!";
        } else {
            throw new Exception("Error adding ride: " . $stmt->error);
        }

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$page_title = 'Add New Ride';
ob_start();
?>

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Add New Ride
                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="mt-4 p-4 rounded-md bg-green-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="mt-4 p-4 rounded-md bg-red-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Ride Form -->
        <div class="mt-6 bg-white shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" class="space-y-6">
                    <!-- Pickup and Dropoff Locations -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="pickup_location" class="block text-sm font-medium text-gray-700">
                                Pickup Location
                            </label>
                            <div class="mt-1">
                                <input type="text" name="pickup_location" id="pickup_location" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Enter pickup location">
                            </div>
                        </div>

                        <div>
                            <label for="dropoff_location" class="block text-sm font-medium text-gray-700">
                                Dropoff Location
                            </label>
                            <div class="mt-1">
                                <input type="text" name="dropoff_location" id="dropoff_location" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Enter dropoff location">
                            </div>
                        </div>
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="ride_date" class="block text-sm font-medium text-gray-700">
                                Ride Date
                            </label>
                            <div class="mt-1">
                                <input type="date" name="ride_date" id="ride_date" required
                                       min="<?php echo date('Y-m-d'); ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="ride_time" class="block text-sm font-medium text-gray-700">
                                Ride Time
                            </label>
                            <div class="mt-1">
                                <input type="time" name="ride_time" id="ride_time" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Seats and Fare -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="available_seats" class="block text-sm font-medium text-gray-700">
                                Available Seats
                            </label>
                            <div class="mt-1">
                                <input type="number" name="available_seats" id="available_seats" required
                                       min="1" max="8"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Number of available seats">
                            </div>
                        </div>

                        <div>
                            <label for="fare_per_seat" class="block text-sm font-medium text-gray-700">
                                Fare per Seat (₹)
                            </label>
                            <div class="mt-1">
                                <input type="number" name="fare_per_seat" id="fare_per_seat" required
                                       min="1" step="0.01"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       placeholder="Enter fare amount">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Ride
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side validation
document.querySelector('form').addEventListener('submit', function(e) {
    const rideDateTime = new Date(
        document.getElementById('ride_date').value + ' ' + 
        document.getElementById('ride_time').value
    );
    
    if (rideDateTime < new Date()) {
        e.preventDefault();
        alert('Ride date and time must be in the future');
    }
});
</script>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 