<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not logged in or not an admin, redirect to the login page
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscriptions - eTaxi Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

    <!-- Admin Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-50 top-0">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a class="text-2xl font-bold text-gray-800" href="/admin/dashboard.php">
                <i class="fas fa-user-shield"></i> eTaxi Admin
            </a>
            <div class="hidden md:flex space-x-6 items-center">
                <a href="/admin/dashboard.php" class="text-gray-600 hover:text-blue-500">Dashboard</a>
                <a href="/admin/manage_passengers.php" class="text-gray-600 hover:text-blue-500">Passengers</a>
                <a href="/admin/manage_drivers.php" class="text-gray-600 hover:text-blue-500">Drivers</a>
                <a href="/admin/manage_subscriptions.php" class="text-blue-500 font-bold">Subscriptions</a>
                <a href="/admin/analytics.php" class="text-gray-600 hover:text-blue-500">Analytics</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Manage Subscription Plans</h1>
            <button id="add-plan-btn" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">Add New Plan</button>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3">ID</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Price</th>
                            <th class="p-3">Max Rides</th>
                            <th class="p-3">Max KM per Ride</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="plans-table-body">
                        <!-- Subscription plan data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const plansTableBody = document.getElementById('plans-table-body');
            const addPlanBtn = document.getElementById('add-plan-btn');

            const fetchPlans = async () => {
                try {
                    const response = await axios.get('/api/admin.php?action=get_subscription_plans');
                    if (response.data.success) {
                        renderPlans(response.data.data);
                    } else {
                        plansTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-red-500">${response.data.message}</td></tr>`;
                    }
                } catch (error) {
                    plansTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-red-500">An error occurred.</td></tr>`;
                }
            };

            const renderPlans = (plans) => {
                plansTableBody.innerHTML = '';
                if (plans.length === 0) {
                    plansTableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4">No subscription plans found.</td></tr>`;
                    return;
                }

                plans.forEach(plan => {
                    const row = `
                        <tr>
                            <td class="p-3">${plan.plan_id}</td>
                            <td class="p-3">${plan.plan_name}</td>
                            <td class="p-3">${plan.price}</td>
                            <td class="p-3">${plan.max_rides}</td>
                            <td class="p-3">${plan.max_km_per_ride}</td>
                            <td class="p-3">
                                <button class="bg-yellow-500 text-white font-bold py-1 px-3 rounded-full text-xs edit-plan-btn" data-id="${plan.plan_id}" data-name="${plan.plan_name}" data-price="${plan.price}" data-rides="${plan.max_rides}" data-km="${plan.max_km_per_ride}">Edit</button>
                                <button class="bg-red-500 text-white font-bold py-1 px-3 rounded-full text-xs delete-plan-btn" data-id="${plan.plan_id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    plansTableBody.innerHTML += row;
                });

                document.querySelectorAll('.edit-plan-btn').forEach(button => {
                    button.addEventListener('click', handleEditPlan);
                });
                document.querySelectorAll('.delete-plan-btn').forEach(button => {
                    button.addEventListener('click', handleDeletePlan);
                });
            };

            addPlanBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Add New Subscription Plan',
                    html: `
                        <input id="swal-plan_name" class="swal2-input" placeholder="Plan Name">
                        <input id="swal-price" class="swal2-input" placeholder="Price">
                        <input id="swal-max_rides" class="swal2-input" placeholder="Max Rides">
                        <input id="swal-max_km_per_ride" class="swal2-input" placeholder="Max KM per Ride">
                    `,
                    focusConfirm: false,
                    preConfirm: () => {
                        return {
                            plan_name: document.getElementById('swal-plan_name').value,
                            price: document.getElementById('swal-price').value,
                            max_rides: document.getElementById('swal-max_rides').value,
                            max_km_per_ride: document.getElementById('swal-max_km_per_ride').value
                        }
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.post('/api/admin.php', { action: 'add_subscription_plan', ...result.value });
                            if (response.data.success) {
                                Swal.fire('Success', 'Plan added successfully.', 'success');
                                fetchPlans();
                            } else {
                                throw new Error(response.data.message);
                            }
                        } catch (error) {
                            Swal.fire('Error', error.message || 'Could not add plan.', 'error');
                        }
                    }
                });
            });

            const handleEditPlan = (event) => {
                const planId = event.target.dataset.id;
                const planName = event.target.dataset.name;
                const price = event.target.dataset.price;
                const maxRides = event.target.dataset.rides;
                const maxKm = event.target.dataset.km;

                Swal.fire({
                    title: 'Edit Subscription Plan',
                    html: `
                        <input id="swal-plan_name" class="swal2-input" value="${planName}">
                        <input id="swal-price" class="swal2-input" value="${price}">
                        <input id="swal-max_rides" class="swal2-input" value="${maxRides}">
                        <input id="swal-max_km_per_ride" class="swal2-input" value="${maxKm}">
                    `,
                    focusConfirm: false,
                    preConfirm: () => {
                        return {
                            plan_id: planId,
                            plan_name: document.getElementById('swal-plan_name').value,
                            price: document.getElementById('swal-price').value,
                            max_rides: document.getElementById('swal-max_rides').value,
                            max_km_per_ride: document.getElementById('swal-max_km_per_ride').value
                        }
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.post('/api/admin.php', { action: 'update_subscription_plan', ...result.value });
                            if (response.data.success) {
                                Swal.fire('Success', 'Plan updated successfully.', 'success');
                                fetchPlans();
                            } else {
                                throw new Error(response.data.message);
                            }
                        } catch (error) {
                            Swal.fire('Error', error.message || 'Could not update plan.', 'error');
                        }
                    }
                });
            };

            const handleDeletePlan = (event) => {
                const planId = event.target.dataset.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.post('/api/admin.php', { action: 'delete_subscription_plan', plan_id: planId });
                            if (response.data.success) {
                                Swal.fire('Deleted!', 'The plan has been deleted.', 'success');
                                fetchPlans();
                            } else {
                                throw new Error(response.data.message);
                            }
                        } catch (error) {
                            Swal.fire('Error', error.message || 'Could not delete plan.', 'error');
                        }
                    }
                });
            };

            fetchPlans();
        });
    </script>

</body>
</html>