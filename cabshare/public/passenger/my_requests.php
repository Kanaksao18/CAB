<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isPassenger()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

// Get user's ride requests
$stmt = $conn->prepare("
    SELECT r.*, 
           COALESCE(COUNT(DISTINCT o.id), 0) as offer_count
    FROM ride_requests r
    LEFT JOIN rides o ON r.id = o.request_id
    WHERE r.passenger_id = ?
    GROUP BY r.id
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$requests = $stmt->get_result();

$page_title = 'My Ride Requests';
ob_start();
?>

<div class="max-w-6xl mx-auto mt-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">My Ride Requests</h2>
        <a href="post_request.php" 
           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            Post New Request
        </a>
    </div>

    <div class="grid gap-6">
        <?php if ($requests->num_rows > 0): ?>
            <?php while ($request = $requests->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-semibold">
                                <?php echo htmlspecialchars($request['pickup_location']); ?> → 
                                <?php echo htmlspecialchars($request['dropoff_location']); ?>
                            </h3>
                            <p class="text-gray-600">
                                <?php echo date('F j, Y', strtotime($request['ride_date'])); ?> at 
                                <?php echo date('g:i A', strtotime($request['ride_time'])); ?>
                            </p>
                            <div class="mt-2 space-y-1">
                                <p>
                                    <span class="font-medium">Seats needed:</span> 
                                    <?php echo $request['num_seats']; ?>
                                </p>
                                <p>
                                    <span class="font-medium">Max price per seat:</span> 
                                    ₹<?php echo number_format($request['max_price_per_seat'], 2); ?>
                                </p>
                                <p>
                                    <span class="font-medium">Driver offers:</span> 
                                    <?php echo $request['offer_count']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?php echo $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($request['status'] === 'accepted' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800'); ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                            <?php if ($request['status'] === 'pending'): ?>
                                <a href="view_offers.php?request_id=<?php echo $request['id']; ?>" 
                                   class="mt-4 text-blue-600 hover:text-blue-800">
                                    View Offers (<?php echo $request['offer_count']; ?>)
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                You haven't posted any ride requests yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 