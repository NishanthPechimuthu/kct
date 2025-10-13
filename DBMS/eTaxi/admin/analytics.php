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
    <title>Analytics - eTaxi Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="/admin/manage_subscriptions.php" class="text-gray-600 hover:text-blue-500">Subscriptions</a>
                <a href="/admin/analytics.php" class="text-blue-500 font-bold">Analytics</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Analytics</h1>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Rides per Day (Last 30 Days)</h2>
                <canvas id="rides-chart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Revenue per Month (Last 12 Months)</h2>
                <canvas id="revenue-chart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg col-span-1 lg:col-span-2">
                <h2 class="text-2xl font-bold mb-4">User Registrations per Month (Last 12 Months)</h2>
                <canvas id="users-chart"></canvas>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await axios.get('/api/admin.php?action=get_analytics_data');
                if (response.data.success) {
                    const data = response.data.data;
                    renderRidesChart(data.rides_per_day);
                    renderRevenueChart(data.revenue_per_month);
                    renderUsersChart(data.users_per_month);
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error('Failed to load analytics data:', error);
            }

            function renderRidesChart(chartData) {
                const ctx = document.getElementById('rides-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(d => d.date),
                        datasets: [{
                            label: 'Rides',
                            data: chartData.map(d => d.count),
                            borderColor: '#3B82F6',
                            tension: 0.1
                        }]
                    }
                });
            }

            function renderRevenueChart(chartData) {
                const ctx = document.getElementById('revenue-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.map(d => d.month),
                        datasets: [{
                            label: 'Revenue',
                            data: chartData.map(d => d.total),
                            backgroundColor: '#10B981'
                        }]
                    }
                });
            }

            function renderUsersChart(chartData) {
                const ctx = document.getElementById('users-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.map(d => d.month),
                        datasets: [{
                            label: 'New Users',
                            data: chartData.map(d => d.count),
                            backgroundColor: '#F59E0B'
                        }]
                    }
                });
            }
        });
    </script>

</body>
</html>