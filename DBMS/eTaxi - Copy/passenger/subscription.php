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
    <title>Subscription - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

    <!-- Passenger Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8 text-center">Subscription Management</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- Current Status -->
            <div id="current-plan-container" class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-4">Your Current Plan</h2>
                <div id="current-plan-details" class="text-center p-4 rounded-lg bg-gray-100">Loading...</div>
            </div>

            <!-- Available Plans -->
            <div id="available-plans-container" class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-4">Available Plans</h2>
                <div id="plans-list" class="space-y-6">Loading plans...</div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            // ... (existing code to load plans) ...
            const currentPlanDetails = document.getElementById('current-plan-details');
            const plansList = document.getElementById('plans-list');

            // Fetch and display current subscription
            try {
                const response = await axios.get('/api/passenger.php');
                const sub = response.data.data.subscription;
                if (sub) {
                    currentPlanDetails.innerHTML = `
                        <p class="font-bold text-green-800">${sub.plan_name} - Active</p>
                        <p>Renews on: ${new Date(sub.end_date).toLocaleDateString()}</p>
                        <p>You have <span class="font-bold">${sub.max_rides - sub.rides_taken} / ${sub.max_rides}</span> rides remaining.</p>
                    `;
                    currentPlanDetails.classList.replace('bg-gray-100', 'bg-green-100');
                } else {
                    currentPlanDetails.innerHTML = '<p class="font-bold">No Active Subscription</p><p>Choose a plan to get started!</p>';
                }
            } catch (error) {
                currentPlanDetails.innerHTML = '<p class="text-red-500">Could not load current plan.</p>';
            }

            // Fetch and display available plans
            try {
                const response = await axios.get('/api/subscriptions.php');
                const plans = response.data.data;
                plansList.innerHTML = '';
                if (plans.length > 0) {
                    plans.forEach(plan => {
                        const planEl = `
                            <div class="border p-4 rounded-lg">
                                <h3 class="text-xl font-bold">${plan.plan_name}</h3>
                                <p class="text-3xl font-extrabold my-2">₹${plan.price}<span class="text-lg font-normal">/month</span></p>
                                <ul class="text-gray-600 text-sm space-y-1">
                                    <li>${plan.max_rides} rides included</li>
                                    <li>Up to ${plan.max_km_per_ride}km per ride</li>
                                    <li>${plan.description || ''}</li>
                                </ul>
                                <button data-plan-id="${plan.plan_id}" data-price="${plan.price}" data-plan-name="${plan.plan_name}" class="subscribe-btn mt-4 w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">Subscribe</button>
                            </div>
                        `;
                        plansList.innerHTML += planEl;
                    });
                }
                document.querySelectorAll('.subscribe-btn').forEach(button => {
                    button.addEventListener('click', handleSubscription);
                });
            } catch (error) {
                plansList.innerHTML = '<p class="text-red-500">Could not load subscription plans.</p>';
            }
        });

        function handleSubscription(event) {
            const planId = event.target.dataset.planId;
            const price = event.target.dataset.price;
            const planName = event.target.dataset.planName;

            Swal.fire({
                title: 'Confirm Subscription',
                html: `You are about to subscribe to the <b>${planName}</b> plan for <b>₹${price}</b>.`, 
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Proceed to Payment',
                confirmButtonColor: '#3B82F6',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await axios.post('/api/subscriptions.php', { plan_id: planId });
                        if (response.data.success) {
                            const paymentId = response.data.payment_id;
                            window.location.href = `/passenger/payment.php?payment_id=${paymentId}&amount=${price}&description=${planName} Plan`;
                        } else {
                            throw new Error(response.data.message);
                        }
                    } catch (error) {
                        Swal.fire('Subscription Failed', error.message || 'An unknown error occurred.', 'error');
                    }
                }
            });
        }
    </script>

</body>
</html>