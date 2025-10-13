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
    <title>Navigation - eTaxi Driver</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 500px; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Driver Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a class="text-2xl font-bold text-gray-800" href="/driver/dashboard.php">
                <i class="fas fa-taxi"></i> eTaxi Driver
            </a>
            <div class="hidden md:flex space-x-6 items-center">
                <a href="/driver/dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
                <a href="/driver/ride_requests.php" class="text-gray-600 hover:text-blue-500">Ride Requests</a>
                <a href="/driver/maintenance.php" class="text-gray-600 hover:text-blue-500">Maintenance</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Ride Navigation</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Map -->
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-lg">
                <div id="map"></div>
            </div>

            <!-- Ride Details -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Ride Details</h2>
                <div id="ride-details"></div>

                <!-- OTP + Ride Controls -->
                <div id="otp-section" class="mt-4">
                    <div id="otp-input-container">
                        <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP to Start Ride</label>
                        <div class="flex gap-2 mt-1">
                            <input type="text" id="otp" name="otp" class="w-full px-3 py-2 border rounded-lg" placeholder="4-digit OTP">
                            <button id="start-ride-btn" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">Start Ride</button>
                        </div>
                    </div>

                    <div id="ride-status-message" class="hidden mt-2"></div>

                    <button id="complete-ride-btn" class="hidden mt-4 w-full bg-red-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-red-600">Complete Ride</button>
                </div>

                <!-- Google Maps link -->
                <a id="google-maps-link" href="#" target="_blank" class="hidden mt-4 w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600 transition-colors duration-300 inline-block text-center">
                    Open in Google Maps
                </a>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const rideId = urlParams.get('ride_id');

            if (!rideId) {
                document.getElementById('map').innerHTML = '<p class="text-red-500">No ride ID provided.</p>';
                return;
            }

            const map = L.map('map').setView([11.0168, 76.9558], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            try {
                const response = await axios.get(`/api/driver.php?action=get_ride_details&ride_id=${rideId}`);
                if (response.data.success) {
                    const ride = response.data.data;
                    const pickup = [ride.pickup_location_lat, ride.pickup_location_lng];
                    const dropoff = [ride.dropoff_location_lat, ride.dropoff_location_lng];

                    L.marker(pickup).addTo(map).bindPopup('Pickup Location');
                    L.marker(dropoff).addTo(map).bindPopup('Drop-off Location');

                    // Fetch route from OSRM
                    const routeUrl = `https://router.project-osrm.org/route/v1/driving/${pickup[1]},${pickup[0]};${dropoff[1]},${dropoff[0]}?overview=full&geometries=geojson`;
                    const routeResponse = await axios.get(routeUrl);
                    const route = routeResponse.data.routes[0].geometry.coordinates;
                    const latLngs = route.map(coord => [coord[1], coord[0]]);

                    L.polyline(latLngs, { color: 'blue' }).addTo(map);
                    map.fitBounds([pickup, dropoff]);

                    // Update ride details
                    document.getElementById('ride-details').innerHTML = `
                        <p class="text-gray-600 mb-2"><b>From:</b> ${ride.pickup_address}</p>
                        <p class="text-gray-600 mb-2"><b>To:</b> ${ride.dropoff_address}</p>
                        <p class="text-gray-600 mb-2"><b>Passenger:</b> ${ride.passenger_name}</p>
                        <p class="text-lg font-bold text-green-600">Fare: ₹${ride.fare}</p>
                    `;

                    // Update Google Maps link
                    const gmapLink = document.getElementById('google-maps-link');
                    gmapLink.href = `https://www.google.com/maps/dir/?api=1&origin=${pickup[0]},${pickup[1]}&destination=${dropoff[0]},${dropoff[1]}`;
                    gmapLink.classList.remove('hidden');
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                document.getElementById('map').innerHTML = `<p class="text-red-500">Could not load ride details: ${error.message}</p>`;
            }

            // ===== Ride Start (OTP) =====
            document.getElementById('start-ride-btn').addEventListener('click', async () => {
                const otp = document.getElementById('otp').value;
                try {
                    const response = await axios.post('/api/driver.php', {
                        action: 'verify_otp_and_start_ride',
                        ride_id: rideId,
                        otp: otp
                    });

                    if (response.data.success) {
                        document.getElementById('otp-input-container').classList.add('hidden');
                        document.getElementById('complete-ride-btn').classList.remove('hidden');
                        const statusMessage = document.getElementById('ride-status-message');
                        statusMessage.innerHTML = '<p class="text-green-500 font-bold">✅ Ride started!</p>';
                        statusMessage.classList.remove('hidden');
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    alert('Failed to start ride: ' + error.message);
                }
            });

            // ===== Ride Complete =====
            document.getElementById('complete-ride-btn').addEventListener('click', async () => {
                try {
                    const response = await axios.post('/api/driver.php', {
                        action: 'complete_ride',
                        ride_id: rideId
                    });

                    if (response.data.success) {
                        document.getElementById('complete-ride-btn').classList.add('hidden');
                        const statusMessage = document.getElementById('ride-status-message');
                        statusMessage.innerHTML = '<p class="text-blue-500 font-bold">🎉 Ride Completed!</p>';
                        statusMessage.classList.remove('hidden');
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    alert('Failed to complete ride: ' + error.message);
                }
            });
        });
    </script>

</body>
</html>
