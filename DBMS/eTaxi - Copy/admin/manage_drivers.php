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
    <title>Manage Drivers - eTaxi Admin</title>
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
                <a href="/admin/manage_drivers.php" class="text-blue-500 font-bold">Drivers</a>
                <a href="/admin/manage_subscriptions.php" class="text-gray-600 hover:text-blue-500">Subscriptions</a>
                <a href="/admin/analytics.php" class="text-gray-600 hover:text-blue-500">Analytics</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Manage Drivers</h1>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3">ID</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Vehicle</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Maintenance Doc</th>
                            <th class="p-3">Next Due Date</th>
                            <th class="p-3">Maintenance Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="drivers-table-body">
                        <!-- Driver data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const driversTableBody = document.getElementById('drivers-table-body');

            const fetchDrivers = async () => {
                try {
                    const response = await axios.get('/api/admin.php?action=get_drivers');
                    if (response.data.success) {
                        renderDrivers(response.data.data);
                    } else {
                        driversTableBody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-red-500">${response.data.message}</td></tr>`;
                    }
                } catch (error) {
                    driversTableBody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-red-500">An error occurred.</td></tr>`;
                }
            };

            const renderDrivers = (drivers) => {
                driversTableBody.innerHTML = '';
                if (drivers.length === 0) {
                    driversTableBody.innerHTML = `<tr><td colspan="8" class="text-center p-4">No drivers found.</td></tr>`;
                    return;
                }

                drivers.forEach(d => {
                    const maintenanceStatus = d.approval_status || 'N/A';
                    let actionButtons = '';
                    if (d.approval_status === 'pending') {
                        actionButtons = `
                            <button class="bg-green-500 text-white font-bold py-1 px-3 rounded-full text-xs update-maint-btn" data-id="${d.maintenance_id}" data-status="approved">Approve</button>
                            <button class="bg-red-500 text-white font-bold py-1 px-3 rounded-full text-xs update-maint-btn" data-id="${d.maintenance_id}" data-status="rejected">Reject</button>
                        `;
                    }

                    const row = `
                        <tr>
                            <td class="p-3">${d.user_id}</td>
                            <td class="p-3">${d.full_name}</td>
                            <td class="p-3">${d.vehicle_details}</td>
                            <td class="p-3">${d.current_status}</td>
                            <td class="p-3">${d.document_type || 'N/A'}</td>
                            <td class="p-3">${d.next_due_date ? new Date(d.next_due_date).toLocaleDateString() : 'N/A'}</td>
                            <td class="p-3">${maintenanceStatus}</td>
                            <td class="p-3">${actionButtons}</td>
                        </tr>
                    `;
                    driversTableBody.innerHTML += row;
                });

                document.querySelectorAll('.update-maint-btn').forEach(button => {
                    button.addEventListener('click', handleUpdateMaintenance);
                });
            };

            const handleUpdateMaintenance = async (event) => {
                const maintenanceId = event.target.dataset.id;
                const newStatus = event.target.dataset.status;

                try {
                    const response = await axios.post('/api/admin.php', { 
                        action: 'update_maintenance_status', 
                        maintenance_id: maintenanceId, 
                        status: newStatus
                    });

                    if (response.data.success) {
                        Swal.fire('Success', 'Maintenance status updated.', 'success');
                        fetchDrivers(); // Refresh the table
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message || 'Could not update status.', 'error');
                }
            };

            fetchDrivers();
        });
    </script>

</body>
</html>