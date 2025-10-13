<?php
session_start();

// Check if the user is logged in and is a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    // If not logged in or not a driver, redirect to the login page
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

    <!-- Driver Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a class="text-2xl font-bold text-gray-800" href="/driver/dashboard.php">
                <i class="fas fa-taxi"></i> eTaxi Driver
            </a>
            <div class="hidden md:flex space-x-6 items-center">
                <a href="/driver/dashboard.php" class="text-blue-500 font-bold">Dashboard</a>
                <a href="/driver/ride_requests.php" class="text-gray-600 hover:text-blue-500">Ride Requests</a>
                <a href="/driver/maintenance.php" class="text-gray-600 hover:text-blue-500">Maintenance</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Your Dashboard</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-xl font-bold mb-2">Total Earnings</h2>
                <p id="total-earnings" class="text-3xl font-extrabold text-green-600">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-xl font-bold mb-2">Completed Rides</h2>
                <p id="completed-rides" class="text-3xl font-extrabold">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <h2 class="text-xl font-bold mb-2">Your Rating</h2>
                <p id="driver-rating" class="text-3xl font-extrabold">Loading...</p>
            </div>
        </div>

        <!-- Assigned Ride -->
        <div id="assigned-ride-container" class="bg-white p-6 rounded-lg shadow-lg mb-12">
            <h2 class="text-2xl font-bold mb-4">Current Assigned Ride</h2>
            <div id="assigned-ride-details">
                <!-- Assigned ride details will be loaded here -->
            </div>
        </div>

        <!-- Ride History -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Recent Ride History</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3">Date</th>
                            <th class="p-3">From</th>
                            <th class="p-3">To</th>
                            <th class="p-3">Fare</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody id="ride-history-body">
                        <!-- Ride history will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Last Maintenance Details -->
        <div class="bg-white p-6 rounded-lg shadow-lg mt-12">
            <h2 class="text-2xl font-bold mb-4">Last Maintenance Details</h2>
            <div id="maintenance-details">
                <!-- Maintenance details will be loaded here -->
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const totalEarningsEl = document.getElementById('total-earnings');
            const completedRidesEl = document.getElementById('completed-rides');
            const driverRatingEl = document.getElementById('driver-rating');
            const assignedRideDetails = document.getElementById('assigned-ride-details');
            const rideHistoryBody = document.getElementById('ride-history-body');
            const maintenanceDetails = document.getElementById('maintenance-details');

            try {
                const response = await axios.get('/api/driver.php?action=get_dashboard_data');
                if (response.data.success) {
                    const data = response.data.data;

                    totalEarningsEl.textContent = `₹${data.total_earnings}`;
                    completedRidesEl.textContent = data.completed_rides;
                    driverRatingEl.textContent = data.rating;

                    // Render assigned ride
                    if (data.assigned_ride) {
                        const ride = data.assigned_ride;
                        assignedRideDetails.innerHTML = `
                            <div class="border p-4 rounded-lg">
                                <h3 class="font-bold text-lg">Ride #${ride.ride_id} - ${ride.ride_status}</h3>
                                <p><b>From:</b> ${ride.pickup_address}</p>
                                <p><b>To:</b> ${ride.dropoff_address}</p>
                                <a href="/driver/navigation.php?ride_id=${ride.ride_id}" class="text-blue-500 hover:underline">Go to Navigation</a>
                            </div>
                        `;
                    } else {
                        assignedRideDetails.innerHTML = '<p class="text-gray-500">No ride currently assigned.</p>';
                    }

                    // Render ride history
                    rideHistoryBody.innerHTML = '';
                    if (data.ride_history.length > 0) {
                        data.ride_history.forEach(ride => {
                            const row = `
                                <tr>
                                    <td class="p-3">${new Date(ride.end_time).toLocaleDateString()}</td>
                                    <td class="p-3">${ride.pickup_address}</td>
                                    <td class="p-3">${ride.dropoff_address}</td>
                                    <td class="p-3">₹${ride.fare}</td>
                                    <td class="p-3">${ride.ride_status}</td>
                                </tr>
                            `;
                            rideHistoryBody.innerHTML += row;
                        });
                    } else {
                        rideHistoryBody.innerHTML = '<tr><td colspan="5" class="text-center p-4">No completed rides yet.</td></tr>';
                    }

                    // Render maintenance details
                    if (data.last_maintenance) {
                        const maintenance = data.last_maintenance;
                        maintenanceDetails.innerHTML = `
                            <div class="border p-4 rounded-lg">
                                <h3 class="font-bold text-lg">${maintenance.document_type}</h3>
                                <p><b>Upload Date:</b> ${new Date(maintenance.upload_date).toLocaleDateString()}</p>
                                <p><b>Next Due Date:</b> ${new Date(maintenance.next_due_date).toLocaleDateString()}</p>
                            </div>
                        `;
                    } else {
                        maintenanceDetails.innerHTML = '<p class="text-gray-500">No maintenance records found.</p>';
                    }

                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error('Failed to load dashboard data:', error);
                // Handle error display
            }
        });
    </script>

</body>
</html>