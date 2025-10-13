
<?php
session_start();

// Check if the user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto px-6 py-24">
        <div class="w-full max-w-lg bg-white rounded-lg shadow-md p-8 mx-auto">
            <h1 class="text-3xl font-bold text-center">Complete Your Profile</h1>
            <p class="text-gray-600 text-center">Please provide your license and vehicle details to continue.</p>

            <form id="complete-profile-form" class="mt-8">
                <div class="space-y-4">
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                        <input type="text" id="license_number" name="license_number" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label for="vehicle_details" class="block text-sm font-medium text-gray-700">Vehicle Details</label>
                        <input type="text" id="vehicle_details" name="vehicle_details" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="e.g., Toyota Prius - 2022 - White" required>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600">Save and Continue</button>
                </div>
            </form>

            <div id="form-status" class="mt-4 text-center"></div>
        </div>
    </main>

    <script>
        document.getElementById('complete-profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const licenseNumber = document.getElementById('license_number').value;
            const vehicleDetails = document.getElementById('vehicle_details').value;
            const formStatus = document.getElementById('form-status');

            try {
                formStatus.innerHTML = '<p class="text-blue-500">Saving...</p>';

                const response = await axios.post('/api/driver.php', {
                    action: 'update_profile',
                    license_number: licenseNumber,
                    vehicle_details: vehicleDetails
                });

                if (response.data.success) {
                    formStatus.innerHTML = '<p class="text-green-500">Profile updated successfully! Redirecting...</p>';
                    setTimeout(() => {
                        window.location.href = '/driver/dashboard.php';
                    }, 2000);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                formStatus.innerHTML = `<p class="text-red-500">Failed to update profile: ${error.message}</p>`;
            }
        });
    </script>
</body>
</html>
