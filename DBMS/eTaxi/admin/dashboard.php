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
    <title>Admin Dashboard - eTaxi</title>
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
                <a href="/admin/dashboard.php" class="text-blue-500 font-bold">Dashboard</a>
                <a href="/admin/manage_passengers.php" class="text-gray-600 hover:text-blue-500">Passengers</a>
                <a href="/admin/manage_drivers.php" class="text-gray-600 hover:text-blue-500">Drivers</a>
                <a href="/admin/manage_subscriptions.php" class="text-gray-600 hover:text-blue-500">Subscriptions</a>
                <a href="/admin/analytics.php" class="text-gray-600 hover:text-blue-500">Analytics</a>
                <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Admin Dashboard</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">Total Rides</h2>
                <p id="total-rides" class="text-3xl font-extrabold">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">Active Subscriptions</h2>
                <p id="active-subscriptions" class="text-3xl font-extrabold">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">Active Drivers</h2>
                <p id="active-drivers" class="text-3xl font-extrabold">Loading...</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">Active Passengers</h2>
                <p id="active-passengers" class="text-3xl font-extrabold">Loading...</p>
            </div>
            <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">Platform Earnings</h2>
                <p id="platform-earnings" class="text-3xl font-extrabold">Loading...</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Rides per Day (Last 30 Days)</h2>
                <canvas id="rides-chart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Revenue per Month (Last 12 Months)</h2>
                <canvas id="revenue-chart"></canvas>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            // Fetch stats
            try {
                const response = await axios.get('/api/admin.php?action=get_dashboard_stats');
                if (response.data.success) {
                    const stats = response.data.data;
                    document.getElementById('total-rides').textContent = stats.total_rides;
                    document.getElementById('active-subscriptions').textContent = stats.active_subscriptions;
                    document.getElementById('active-drivers').textContent = stats.active_drivers;
                    document.getElementById('active-passengers').textContent = stats.active_passengers;
                    document.getElementById('platform-earnings').textContent = `₹${stats.platform_earnings}`;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error('Failed to load dashboard stats:', error);
            }

            // Fetch analytics data for charts
            try {
                const response = await axios.get('/api/admin.php?action=get_analytics_data');
                if (response.data.success) {
                    const analytics = response.data.data;

                    // Rides per day chart
                    const ridesCtx = document.getElementById('rides-chart').getContext('2d');
                    new Chart(ridesCtx, {
                        type: 'bar',
                        data: {
                            labels: analytics.rides_per_day.map(d => d.date),
                            datasets: [{
                                label: 'Rides',
                                data: analytics.rides_per_day.map(d => d.count),
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Revenue per month chart
                    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: analytics.revenue_per_month.map(d => d.month),
                            datasets: [{
                                label: 'Platform Earnings (₹)',
                                data: analytics.revenue_per_month.map(d => d.total),
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error('Failed to load analytics data:', error);
            }
        });
    </script>

</body>
</html>
