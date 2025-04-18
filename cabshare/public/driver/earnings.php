<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isDriver()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Get earnings summary
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT r.id) as total_rides,
        COUNT(DISTINCT b.id) as total_bookings,
        SUM(b.total_fare) as total_earnings,
        AVG(b.total_fare) as average_earning_per_ride
    FROM rides r
    LEFT JOIN bookings b ON r.id = b.ride_id
    WHERE r.driver_id = ? AND r.status = 'completed'
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$earnings = $stmt->get_result()->fetch_assoc();

// Get recent completed rides
$stmt = $conn->prepare("
    SELECT r.*, 
           COUNT(b.id) as total_bookings,
           SUM(b.total_fare) as ride_earnings
    FROM rides r
    LEFT JOIN bookings b ON r.id = b.ride_id
    WHERE r.driver_id = ? AND r.status = 'completed'
    GROUP BY r.id
    ORDER BY r.ride_date DESC, r.ride_time DESC
    LIMIT 10
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recent_rides = $stmt->get_result();

$page_title = 'My Earnings';
ob_start();
?>

<div class="max-w-6xl mx-auto mt-8 px-4">
    <h2 class="text-2xl font-bold mb-6">Earnings Summary</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-600">Total Rides</h3>
            <p class="text-3xl font-bold mt-2"><?php echo $earnings['total_rides']; ?></p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-600">Total Bookings</h3>
            <p class="text-3xl font-bold mt-2"><?php echo $earnings['total_bookings']; ?></p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-600">Total Earnings</h3>
            <p class="text-3xl font-bold mt-2">₹<?php echo number_format($earnings['total_earnings'], 2); ?></p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-600">Average per Ride</h3>
            <p class="text-3xl font-bold mt-2">₹<?php echo number_format($earnings['average_earning_per_ride'], 2); ?></p>
        </div>
    </div>

    <h3 class="text-xl font-bold mb-4">Recent Completed Rides</h3>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date & Time
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Route
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Bookings
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Earnings
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($ride = $recent_rides->fetch_assoc()): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo date('M j, Y', strtotime($ride['ride_date'])); ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo date('g:i A', strtotime($ride['ride_time'])); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($ride['pickup_location']); ?> →
                                <?php echo htmlspecialchars($ride['dropoff_location']); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $ride['total_bookings']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₹<?php echo number_format($ride['ride_earnings'], 2); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 