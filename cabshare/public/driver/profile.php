<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || !isDriver()) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Initialize user data array with default values
$user = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'gender' => '',
    'date_of_birth' => '',
    'vehicle_number' => '',
    'vehicle_model' => '',
    'license_number' => '',
    'driving_experience' => 0,
    'vehicle_type' => 'sedan',
    'insurance_number' => ''
];

// First, check and update the driver_details table structure
$alter_table_queries = [
    "ALTER TABLE driver_details ADD COLUMN IF NOT EXISTS vehicle_type ENUM('sedan', 'suv', 'hatchback') NOT NULL DEFAULT 'sedan'",
    "ALTER TABLE driver_details ADD COLUMN IF NOT EXISTS insurance_number VARCHAR(50) NOT NULL DEFAULT ''"
];

foreach ($alter_table_queries as $query) {
    try {
        $conn->query($query);
    } catch (Exception $e) {
        // Log the error but continue execution
        error_log("Error updating table structure: " . $e->getMessage());
    }
}

// Get user and driver data with error handling
$query = "
    SELECT u.*, 
           d.vehicle_number, 
           d.vehicle_model, 
           d.license_number,
           d.driving_experience,
           COALESCE(d.vehicle_type, 'sedan') as vehicle_type,
           COALESCE(d.insurance_number, '') as insurance_number
    FROM users u
    LEFT JOIN driver_details d ON u.id = d.user_id
    WHERE u.id = ? AND u.role = 'driver'
";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user = array_merge($user, $row);
        }
    } else {
        $error_message = "Error executing query: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error_message = "Error preparing query: " . $conn->error;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        // Update basic user information
        $update_user = $conn->prepare("
            UPDATE users 
            SET first_name = ?,
                last_name = ?,
                phone = ?,
                email = ?,
                gender = ?,
                date_of_birth = ?
            WHERE id = ? AND user_type = 'driver'
        ");

        if ($update_user) {
            $update_user->bind_param(
                "ssssssi",
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['phone'],
                $_POST['email'],
                $_POST['gender'],
                $_POST['date_of_birth'],
                $user_id
            );
            $update_user->execute();
        } else {
            throw new Exception("Error preparing user update query: " . $conn->error);
        }

        // Check if driver details exist
        $check_driver = $conn->prepare("SELECT user_id FROM driver_details WHERE user_id = ?");
        if (!$check_driver) {
            throw new Exception("Error preparing check query: " . $conn->error);
        }

        $check_driver->bind_param("i", $user_id);
        $check_driver->execute();
        $driver_exists = $check_driver->get_result()->num_rows > 0;
        $check_driver->close();

        // Prepare the appropriate query based on whether driver details exist
        if ($driver_exists) {
            $driver_query = "
                UPDATE driver_details 
                SET vehicle_number = ?,
                    vehicle_model = ?,
                    license_number = ?,
                    driving_experience = ?,
                    vehicle_type = ?,
                    insurance_number = ?
                WHERE user_id = ?
            ";
        } else {
            $driver_query = "
                INSERT INTO driver_details 
                (vehicle_number, vehicle_model, license_number, driving_experience, vehicle_type, insurance_number, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
        }

        $update_driver = $conn->prepare($driver_query);
        if (!$update_driver) {
            throw new Exception("Error preparing driver update query: " . $conn->error);
        }

        $driving_experience = intval($_POST['driving_experience']);
        
        $update_driver->bind_param(
            "sssissi",
            $_POST['vehicle_number'],
            $_POST['vehicle_model'],
            $_POST['license_number'],
            $driving_experience,
            $_POST['vehicle_type'],
            $_POST['insurance_number'],
            $user_id
        );
        $update_driver->execute();

        $conn->commit();
        $success_message = "Profile updated successfully!";
        
        // Refresh user data
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $user = array_merge($user, $row);
                }
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

$page_title = 'Driver Profile';
ob_start();
?>

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="md:flex md:items-center md:justify-between md:space-x-5 p-6">
                <div class="flex items-start space-x-5">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="h-16 w-16 rounded-full bg-yellow-100 flex items-center justify-center">
                                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="pt-1.5">
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </h1>
                        <p class="text-sm font-medium text-gray-500">Driver Account</p>
                    </div>
                </div>
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

        <!-- Profile Form -->
        <div class="mt-6 bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
                <p class="mt-1 text-sm text-gray-500">Update your personal and vehicle information.</p>
            </div>

            <div class="border-t border-gray-200">
                <form method="POST" class="divide-y divide-gray-200">
                    <!-- Personal Information -->
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                                <input type="text" name="first_name" id="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                                <input type="text" name="last_name" id="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
                                <input type="tel" name="phone" id="phone" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender" id="gender" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" 
                                       value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Driver Information -->
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Vehicle Information</h3>
                        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                            <div>
                                <label for="vehicle_number" class="block text-sm font-medium text-gray-700">Vehicle Number</label>
                                <input type="text" name="vehicle_number" id="vehicle_number" 
                                       value="<?php echo htmlspecialchars($user['vehicle_number']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Vehicle Model</label>
                                <input type="text" name="vehicle_model" id="vehicle_model" 
                                       value="<?php echo htmlspecialchars($user['vehicle_model']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                                <select name="vehicle_type" id="vehicle_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="sedan" <?php echo $user['vehicle_type'] === 'sedan' ? 'selected' : ''; ?>>Sedan</option>
                                    <option value="suv" <?php echo $user['vehicle_type'] === 'suv' ? 'selected' : ''; ?>>SUV</option>
                                    <option value="hatchback" <?php echo $user['vehicle_type'] === 'hatchback' ? 'selected' : ''; ?>>Hatchback</option>
                                </select>
                            </div>

                            <div>
                                <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                                <input type="text" name="license_number" id="license_number" 
                                       value="<?php echo htmlspecialchars($user['license_number']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="insurance_number" class="block text-sm font-medium text-gray-700">Insurance Number</label>
                                <input type="text" name="insurance_number" id="insurance_number" 
                                       value="<?php echo htmlspecialchars($user['insurance_number']); ?>" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="driving_experience" class="block text-sm font-medium text-gray-700">Driving Experience (years)</label>
                                <input type="number" name="driving_experience" id="driving_experience" 
                                       value="<?php echo htmlspecialchars($user['driving_experience']); ?>" required
                                       min="0" step="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="px-6 py-4 flex justify-end">
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Additional Sections -->
        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Change Password Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900">Password</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Ensure your account is using a strong password for security.
                </p>
                <div class="mt-4">
                    <a href="../change_password.php" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Change Password
                    </a>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-red-600">Delete Account</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Permanently delete your account and all associated data.
                </p>
                <div class="mt-4">
                    <button type="button" 
                            onclick="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) window.location.href='delete_account.php';"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include '../../includes/components/layout.php';
?> 