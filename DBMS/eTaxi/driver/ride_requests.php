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
    <title>Ride Requests - eTaxi Driver</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <a href="/driver/ride_requests.php" class="text-blue-500 font-bold">Ride Requests</a>
                <a href="/driver/maintenance.php" class="text-gray-600 hover:text-blue-500">Maintenance</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Incoming Ride Requests</h1>
        <div id="ride-requests-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Ride requests will be dynamically inserted here -->
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rideRequestsContainer = document.getElementById('ride-requests-container');

            const fetchRideRequests = async () => {
                try {
                    const response = await axios.get('/api/driver.php?action=get_ride_requests');
                    if (response.data.success) {
                        renderRideRequests(response.data.data);
                    } else {
                        rideRequestsContainer.innerHTML = `<p class="text-red-500">${response.data.message}</p>`;
                    }
                } catch (error) {
                    rideRequestsContainer.innerHTML = `<p class="text-red-500">An error occurred while fetching ride requests.</p>`;
                    console.error(error);
                }
            };

            const renderRideRequests = (rides) => {
                if (rides.length === 0) {
                    rideRequestsContainer.innerHTML = `<p class="text-gray-500">No ride requests at the moment. Check back soon!</p>`;
                    return;
                }

                rideRequestsContainer.innerHTML = '';
                rides.forEach(ride => {
                    const rideCard = `
                        <div class="bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold">Ride #${ride.ride_id}</h3>
                                <span class="text-lg font-bold text-green-600">₹${ride.fare}</span>
                            </div>
                            <p class="text-gray-600 mb-2"><b>From:</b> ${ride.pickup_address}</p>
                            <p class="text-gray-600 mb-4"><b>To:</b> ${ride.dropoff_address}</p>
                            <p class="text-gray-600 mb-4"><b>Passenger:</b> ${ride.passenger_name}</p>
                            <div class="flex justify-end gap-4">
                                <button class="bg-red-500 text-white font-bold py-2 px-4 rounded-full hover:bg-red-600 transition-colors duration-300 decline-btn" data-ride-id="${ride.ride_id}">Decline</button>
                                <button class="bg-green-500 text-white font-bold py-2 px-4 rounded-full hover:bg-green-600 transition-colors duration-300 accept-btn" data-ride-id="${ride.ride_id}">Accept</button>
                            </div>
                        </div>
                    `;
                    rideRequestsContainer.innerHTML += rideCard;
                });

                document.querySelectorAll('.accept-btn').forEach(button => {
                    button.addEventListener('click', handleAcceptRide);
                });

                document.querySelectorAll('.decline-btn').forEach(button => {
                    button.addEventListener('click', handleDeclineRide);
                });
            };

            const handleDeclineRide = (event) => {
                const rideCard = event.target.closest('.bg-white');
                rideCard.style.transition = 'opacity 0.5s ease';
                rideCard.style.opacity = '0';
                setTimeout(() => {
                    rideCard.remove();
                    if (rideRequestsContainer.children.length === 0) {
                        rideRequestsContainer.innerHTML = `<p class="text-gray-500">No ride requests at the moment. Check back soon!</p>`;
                    }
                }, 500);
            };

            const handleAcceptRide = async (event) => {
                const rideId = event.target.dataset.rideId;
                try {
                    const response = await axios.post('/api/driver.php', { action: 'accept_ride', ride_id: rideId });
                    if (response.data.success) {
                        Swal.fire('Ride Accepted!', 'The ride has been assigned to you.', 'success').then(() => {
                            window.location.href = '/driver/navigation.php?ride_id=' + rideId;
                        });
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message || 'Could not accept the ride.', 'error');
                }
            };

            fetchRideRequests();
            // Refresh ride requests every 30 seconds
            setInterval(fetchRideRequests, 30000);
        });
    </script>

</body>
</html>