<?php
session_start();

// Check if the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    // If not logged in or not a passenger, redirect to the login page
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">

    <!-- Passenger Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Welcome, Passenger!</h1>

        <!-- Subscription & Stats -->
        <div id="stats-grid" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Subscription Status</h2>
                <p id="sub-status" class="text-3xl font-extrabold text-gray-500">Loading...</p>
                <p id="sub-plan" class="text-gray-600"></p>
                <p id="sub-renewal" class="mt-4 text-sm"></p>
                <a href="/passenger/subscription.php" class="text-blue-500 hover:underline mt-2 inline-block">Manage</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Rides Left</h2>
                <p id="rides-left" class="text-3xl font-extrabold">-- <span class="text-lg font-normal text-gray-500">/ --</span></p>
                <p class="text-gray-600">in current billing cycle</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Monthly Spending</h2>
                <p class="text-3xl font-extrabold">₹0</p>
                <p class="text-gray-600">Includes subscription + extras</p>
            </div>
        </div>

        <!-- Ride History -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Recent Rides</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3">Date</th>
                            <th class="p-3">From</th>
                            <th class="p-3">To</th>
                            <th class="p-3">Fare</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ride-history-body">
                        <tr>
                            <td colspan="5" class="text-center p-4">Loading ride history...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await axios.get('/api/passenger.php');
                const data = response.data.data;

                // Update Subscription Info
                const subStatusEl = document.getElementById('sub-status');
                const subPlanEl = document.getElementById('sub-plan');
                const subRenewalEl = document.getElementById('sub-renewal');
                const ridesLeftEl = document.getElementById('rides-left');

                if (data.subscription) {
                    subStatusEl.textContent = 'Active';
                    subStatusEl.className = 'text-3xl font-extrabold text-green-500';
                    subPlanEl.textContent = data.subscription.plan_name;
                    subRenewalEl.textContent = `Renews on: ${new Date(data.subscription.end_date).toLocaleDateString()}`;
                    ridesLeftEl.innerHTML = `${data.subscription.max_rides - data.subscription.rides_taken} <span class="text-lg font-normal text-gray-500">/ ${data.subscription.max_rides}</span>`;
                } else {
                    subStatusEl.textContent = 'Inactive';
                    subPlanEl.textContent = 'No active plan.';
                    ridesLeftEl.innerHTML = 'N/A';
                }

                        const rideHistoryBody = document.getElementById('ride-history-body');
                        rideHistoryBody.innerHTML = ''; // Clear loading message

                        let ongoingRides = [];

                        if (data.ride_history.length > 0) {
                            data.ride_history.forEach(ride => {
                                const statusClass = ride.ride_status === 'completed' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800';
                                const row = `
                                    <tr id="ride-row-${ride.ride_id}">
                                        <td class="p-3">${new Date(ride.request_time).toLocaleDateString()}</td>
                                        <td class="p-3">${ride.pickup_address || 'N/A'}</td>
                                        <td class="p-3">${ride.dropoff_address || 'N/A'}</td>
                                        <td class="p-3">₹${ride.ride_status === 'completed' ? ride.payment_amount : ride.ride_fare}</td>
                                        <td class="p-3"><span class="${statusClass} py-1 px-3 rounded-full text-xs">${ride.ride_status}</span></td>
                                        <td class="p-3" id="ride-actions-${ride.ride_id}">
                                            ${ride.ride_status === 'requested' ? `<button class="bg-red-500 text-white font-bold py-1 px-3 rounded-full text-xs cancel-ride-btn" data-ride-id="${ride.ride_id}">Cancel</button>` : ''}
                                            ${ride.ride_status === 'accepted' ? `<span class="text-sm font-bold">OTP: ${ride.otp}</span>` : ''}
                                            ${(ride.ride_status === 'completed' || ride.ride_status === 'in_progress') && ride.payment_status === 'pending' ? `<a href="/passenger/payment.php?payment_id=${ride.payment_id}&amount=${ride.ride_status === 'completed' ? ride.payment_amount : ride.ride_fare}&description=Ride from ${ride.pickup_address} to ${ride.dropoff_address}" class="bg-blue-500 text-white font-bold py-1 px-3 rounded-full text-xs">Pay Now</a>` : ''}
                                        </td>
                                    </tr>
                                `;
                                rideHistoryBody.innerHTML += row;

                                if (ride.ride_status === 'accepted' || ride.ride_status === 'started') {
                                    ongoingRides.push(ride.ride_id);
                                }
                            });
                        } else {
                            rideHistoryBody.innerHTML = '<tr><td colspan="6" class="text-center p-4">No recent rides found.</td></tr>';
                        }

                        if (ongoingRides.length > 0) {
                            setInterval(() => updateRideStatus(ongoingRides), 5000);
                        }

                    } catch (error) {
                        console.error('Failed to load dashboard data:', error);
                        document.getElementById('stats-grid').innerHTML = '<p class="text-red-500 col-span-3">Could not load dashboard data. Please try logging in again.</p>';
                    }

                    const rideHistoryBody = document.getElementById('ride-history-body');
                    rideHistoryBody.addEventListener('click', async (event) => {
                        if (event.target.classList.contains('cancel-ride-btn')) {
                            const rideId = event.target.dataset.rideId;
                            await cancelRide(rideId, event.target);
                        }
                    });

                    async function cancelRide(rideId, button) {
                        try {
                            const response = await axios.post('/api/rides.php', { 
                                action: 'cancel_by_passenger', 
                                ride_id: rideId 
                            });
                            if (response.data.success) {
                                // Update the UI
                                const row = button.closest('tr');
                                row.querySelector('td:nth-child(5) span').textContent = 'cancelled_by_passenger';
                                row.querySelector('td:nth-child(5) span').className = 'bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs';
                                button.remove();
                            } else {
                                throw new Error(response.data.message);
                            }
                        } catch (error) {
                            alert('Failed to cancel ride: ' + error.message);
                        }
                    }

                    async function updateRideStatus(rideIds) {
                        if (rideIds.length === 0) return;
                        try {
                            const response = await axios.get(`/api/passenger.php?action=poll_rides&ride_ids=${rideIds.join(',')}`);
                            const updatedRides = response.data.data.ride_history;

                            updatedRides.forEach(ride => {
                                const row = document.getElementById(`ride-row-${ride.ride_id}`);
                                if (row) {
                                    const statusCell = row.querySelector('td:nth-child(5) span');
                                    const actionsCell = document.getElementById(`ride-actions-${ride.ride_id}`);

                                    if (statusCell.textContent !== ride.ride_status) {
                                        statusCell.textContent = ride.ride_status;
                                        const statusClass = ride.ride_status === 'completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800';
                                        statusCell.className = `${statusClass} py-1 px-3 rounded-full text-xs`;
                                    }

                                    const fareCell = row.querySelector('td:nth-child(4)');
                                    fareCell.innerHTML = `₹${ride.ride_status === 'completed' ? ride.payment_amount : ride.ride_fare}`;

                                    let actionsHTML = '';
                                    if ((ride.ride_status === 'completed' || ride.ride_status === 'in_progress') && ride.payment_status === 'pending') {
                                        actionsHTML = `<a href="/passenger/payment.php?payment_id=${ride.payment_id}&amount=${ride.ride_status === 'completed' ? ride.payment_amount : ride.ride_fare}&description=Ride from ${ride.pickup_address} to ${ride.dropoff_address}" class="bg-blue-500 text-white font-bold py-1 px-3 rounded-full text-xs">Pay Now</a>`;
                                    } else if (ride.ride_status === 'accepted') {
                                        actionsHTML = `<span class="text-sm font-bold">OTP: ${ride.otp}</span>`;
                                    }
                                    actionsCell.innerHTML = actionsHTML;
                                }
                            });
                        } catch (error) {
                            console.error('Failed to update ride status:', error);
                        }
                    }
                });
    </script>

</body>
</html>