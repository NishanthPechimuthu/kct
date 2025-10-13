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
    <title>Manage Passengers - eTaxi Admin</title>
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
                <a href="/admin/manage_passengers.php" class="text-blue-500 font-bold">Passengers</a>
                <a href="/admin/manage_drivers.php" class="text-gray-600 hover:text-blue-500">Drivers</a>
                <a href="/admin/manage_subscriptions.php" class="text-gray-600 hover:text-blue-500">Subscriptions</a>
                <a href="/admin/analytics.php" class="text-gray-600 hover:text-blue-500">Analytics</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Manage Passengers</h1>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3">ID</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Registered</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="passengers-table-body">
                        <!-- Passenger data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passengersTableBody = document.getElementById('passengers-table-body');

            const fetchPassengers = async () => {
                try {
                    const response = await axios.get('/api/admin.php?action=get_passengers');
                    if (response.data.success) {
                        renderPassengers(response.data.data);
                    } else {
                        passengersTableBody.innerHTML = `<tr><td colspan="7" class="text-center p-4 text-red-500">${response.data.message}</td></tr>`;
                    }
                } catch (error) {
                    passengersTableBody.innerHTML = `<tr><td colspan="7" class="text-center p-4 text-red-500">An error occurred.</td></tr>`;
                }
            };

            const renderPassengers = (passengers) => {
                passengersTableBody.innerHTML = '';
                if (passengers.length === 0) {
                    passengersTableBody.innerHTML = `<tr><td colspan="7" class="text-center p-4">No passengers found.</td></tr>`;
                    return;
                }

                passengers.forEach(p => {
                    const statusClass = p.is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800';
                    const actionText = p.is_active ? 'Block' : 'Unblock';
                    const actionClass = p.is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600';

                    const row = `
                        <tr>
                            <td class="p-3">${p.user_id}</td>
                            <td class="p-3">${p.full_name}</td>
                            <td class="p-3">${p.email}</td>
                            <td class="p-3">${p.phone_number}</td>
                            <td class="p-3">${new Date(p.registration_date).toLocaleDateString()}</td>
                            <td class="p-3"><span class="${statusClass} py-1 px-3 rounded-full text-xs">${p.is_active ? 'Active' : 'Blocked'}</span></td>
                            <td class="p-3">
                                <button class="${actionClass} text-white font-bold py-1 px-3 rounded-full text-xs update-status-btn" data-id="${p.user_id}" data-status="${p.is_active ? 0 : 1}">${actionText}</button>
                            </td>
                        </tr>
                    `;
                    passengersTableBody.innerHTML += row;
                });

                document.querySelectorAll('.update-status-btn').forEach(button => {
                    button.addEventListener('click', handleUpdateStatus);
                });
            };

            const handleUpdateStatus = async (event) => {
                const passengerId = event.target.dataset.id;
                const newStatus = parseInt(event.target.dataset.status);

                try {
                    const response = await axios.post('/api/admin.php', { 
                        action: 'update_passenger_status', 
                        passenger_id: passengerId, 
                        is_active: newStatus === 1
                    });

                    if (response.data.success) {
                        Swal.fire('Success', 'Passenger status updated.', 'success');
                        fetchPassengers(); // Refresh the table
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message || 'Could not update status.', 'error');
                }
            };

            fetchPassengers();
        });
    </script>

</body>
</html>