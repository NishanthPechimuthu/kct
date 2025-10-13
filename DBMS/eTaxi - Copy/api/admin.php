<?php
// /api/admin.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$admin_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_dashboard_stats':
            $stmt = $pdo->query("SELECT COUNT(*) FROM Rides");
            $total_rides = $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM Subscriptions WHERE is_active = 1");
            $active_subscriptions = $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'driver' AND is_active = 1");
            $active_drivers = $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'passenger' AND is_active = 1");
            $active_passengers = $stmt->fetchColumn();

            $stmt = $pdo->query("SELECT SUM(fare) FROM Rides WHERE ride_status = 'completed'");
            $total_revenue = $stmt->fetchColumn();
            $platform_earnings = $total_revenue * 0.15;

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_rides' => $total_rides,
                    'active_subscriptions' => $active_subscriptions,
                    'active_drivers' => $active_drivers,
                    'active_passengers' => $active_passengers,
                    'platform_earnings' => round($platform_earnings, 2)
                ]
            ]);
            break;

        case 'get_passengers':
            $stmt = $pdo->query("SELECT user_id, full_name, email, phone_number, registration_date, is_active FROM Users WHERE role = 'passenger'");
            $passengers = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $passengers]);
            break;

        case 'update_passenger_status':
            $passenger_id = $data['passenger_id'] ?? null;
            $is_active = $data['is_active'] ?? null;

            if (!$passenger_id || !is_bool($is_active)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Passenger ID and status are required.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE Users SET is_active = ? WHERE user_id = ? AND role = 'passenger'");
            $stmt->execute([$is_active, $passenger_id]);

            echo json_encode(['success' => true, 'message' => 'Passenger status updated successfully.']);
            break;

        case 'get_drivers':
            $stmt = $pdo->query("SELECT u.user_id, u.full_name, u.email, d.vehicle_details, d.current_status, dm.document_type, dm.next_due_date, dm.approval_status, dm.maintenance_id FROM Users u JOIN Drivers d ON u.user_id = d.driver_id LEFT JOIN (SELECT * FROM Driver_Maintenance WHERE (driver_id, upload_date) IN (SELECT driver_id, MAX(upload_date) FROM Driver_Maintenance GROUP BY driver_id)) dm ON d.driver_id = dm.driver_id WHERE u.role = 'driver'");
            $drivers = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $drivers]);
            break;

        case 'update_maintenance_status':
            $maintenance_id = $data['maintenance_id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$maintenance_id || !$status) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Maintenance ID and status are required.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE Driver_Maintenance SET approval_status = ?, admin_id = ?, review_date = CURRENT_TIMESTAMP WHERE maintenance_id = ?");
            $stmt->execute([$status, $admin_id, $maintenance_id]);

            echo json_encode(['success' => true, 'message' => 'Maintenance status updated successfully.']);
            break;

        case 'get_subscription_plans':
            $stmt = $pdo->query("SELECT * FROM Subscription_Plans");
            $plans = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $plans]);
            break;

        case 'add_subscription_plan':
            $plan_name = $data['plan_name'] ?? null;
            $price = $data['price'] ?? null;
            $max_rides = $data['max_rides'] ?? null;
            $max_km_per_ride = $data['max_km_per_ride'] ?? null;

            if (!$plan_name || !$price || !$max_rides || !$max_km_per_ride) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All plan details are required.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO Subscription_Plans (plan_name, price, max_rides, max_km_per_ride) VALUES (?, ?, ?, ?)");
            $stmt->execute([$plan_name, $price, $max_rides, $max_km_per_ride]);

            echo json_encode(['success' => true, 'message' => 'Subscription plan added successfully.']);
            break;

        case 'update_subscription_plan':
            $plan_id = $data['plan_id'] ?? null;
            $plan_name = $data['plan_name'] ?? null;
            $price = $data['price'] ?? null;
            $max_rides = $data['max_rides'] ?? null;
            $max_km_per_ride = $data['max_km_per_ride'] ?? null;

            if (!$plan_id || !$plan_name || !$price || !$max_rides || !$max_km_per_ride) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All plan details are required.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE Subscription_Plans SET plan_name = ?, price = ?, max_rides = ?, max_km_per_ride = ? WHERE plan_id = ?");
            $stmt->execute([$plan_name, $price, $max_rides, $max_km_per_ride, $plan_id]);

            echo json_encode(['success' => true, 'message' => 'Subscription plan updated successfully.']);
            break;

        case 'delete_subscription_plan':
            $plan_id = $data['plan_id'] ?? null;

            if (!$plan_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Plan ID is required.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM Subscription_Plans WHERE plan_id = ?");
            $stmt->execute([$plan_id]);

            echo json_encode(['success' => true, 'message' => 'Subscription plan deleted successfully.']);
            break;

        case 'get_analytics_data':
            // Rides per day for the last 30 days
            $rides_per_day_stmt = $pdo->query("SELECT DATE(request_time) as date, COUNT(*) as count FROM Rides WHERE request_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(request_time)");
            $rides_per_day = $rides_per_day_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Revenue per month for the last 12 months (15% platform cut)
            $revenue_per_month_stmt = $pdo->query("SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) * 0.15 as total FROM Payments WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND payment_status = 'completed' GROUP BY DATE_FORMAT(payment_date, '%Y-%m')");
            $revenue_per_month = $revenue_per_month_stmt->fetchAll(PDO::FETCH_ASSOC);

            // User registrations per month for the last 12 months
            $users_per_month_stmt = $pdo->query("SELECT DATE_FORMAT(registration_date, '%Y-%m') as month, COUNT(*) as count FROM Users WHERE registration_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(registration_date, '%Y-%m')");
            $users_per_month = $users_per_month_stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => [
                    'rides_per_day' => $rides_per_day,
                    'revenue_per_month' => $revenue_per_month,
                    'users_per_month' => $users_per_month
                ]
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>