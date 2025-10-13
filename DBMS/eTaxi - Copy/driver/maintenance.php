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
    <title>Vehicle Maintenance - eTaxi Driver</title>
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
                <a href="/driver/ride_requests.php" class="text-gray-600 hover:text-blue-500">Ride Requests</a>
                <a href="/driver/maintenance.php" class="text-blue-500 font-bold">Maintenance</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Vehicle Maintenance</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Upload Document Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6">Upload New Document</h2>
                <form id="maintenance-form" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                        <select id="document_type" name="document_type" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm">
                            <option>Vehicle Service Record</option>
                            <option>Insurance</option>
                            <option>Pollution Certificate</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="next_due_date" class="block text-sm font-medium text-gray-700">Next Due Date</label>
                        <input type="date" id="next_due_date" name="next_due_date" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-6">
                        <label for="document_file" class="block text-sm font-medium text-gray-700">Document File</label>
                        <input type="file" id="document_file" name="document_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600 transition-colors duration-300">Upload Document</button>
                </form>
            </div>

            <!-- Maintenance History -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6">Maintenance History & Reminders</h2>
                <div id="maintenance-history" class="space-y-4">
                    <!-- History will be loaded here -->
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const maintenanceForm = document.getElementById('maintenance-form');
            const maintenanceHistory = document.getElementById('maintenance-history');

            const fetchMaintenanceHistory = async () => {
                try {
                    const response = await axios.get('/api/driver.php?action=get_maintenance_history');
                    if (response.data.success) {
                        renderMaintenanceHistory(response.data.data);
                    } else {
                        maintenanceHistory.innerHTML = `<p class="text-red-500">${response.data.message}</p>`;
                    }
                } catch (error) {
                    maintenanceHistory.innerHTML = `<p class="text-red-500">Could not load maintenance history.</p>`;
                }
            };

            const renderMaintenanceHistory = (history) => {
                maintenanceHistory.innerHTML = '';
                if (history.length === 0) {
                    maintenanceHistory.innerHTML = '<p class="text-gray-500">No maintenance records found.</p>';
                    return;
                }
                history.forEach(record => {
                    const recordEl = `
                        <div class="border p-4 rounded-lg">
                            <div class="flex justify-between">
                                <p class="font-bold">${record.document_type}</p>
                                <p class="text-sm text-gray-500">Due: ${new Date(record.next_due_date).toLocaleDateString()}</p>
                            </div>
                            <p class="text-sm">Status: <span class="font-semibold">${record.approval_status}</span></p>
                        </div>
                    `;
                    maintenanceHistory.innerHTML += recordEl;
                });
            };

            maintenanceForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(maintenanceForm);
                formData.append('action', 'upload_maintenance_document');

                try {
                    const response = await axios.post('/api/driver.php', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        Swal.fire('Upload Successful', 'Your document has been uploaded for review.', 'success');
                        fetchMaintenanceHistory(); // Refresh the list
                        maintenanceForm.reset();
                    } else {
                        throw new Error(response.data.message);
                    }
                } catch (error) {
                    Swal.fire('Upload Failed', error.message || 'An error occurred.', 'error');
                }
            });

            fetchMaintenanceHistory();
        });
    </script>

</body>
</html>