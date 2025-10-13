<?php
// /api/subscriptions.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

$response = ['success' => false, 'message' => 'Invalid request'];

// Ensure user is logged in and is a passenger for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Fetch all available subscription plans
        $stmt = $pdo->query("SELECT plan_id, plan_name, price, max_rides, max_km_per_ride, description FROM Subscription_Plans WHERE is_available = 1");
        $plans = $stmt->fetchAll();
        $response = ['success' => true, 'data' => $plans];

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $plan_id = $data['plan_id'] ?? null;
        $passenger_id = $_SESSION['user_id'];

        if (!$plan_id) {
            http_response_code(400);
            $response = ['success' => false, 'message' => 'Plan ID is required.'];
        } else {
            // Deactivate any existing active subscriptions for the user
            $stmt = $pdo->prepare("UPDATE Subscriptions SET is_active = 0 WHERE passenger_id = ?");
            $stmt->execute([$passenger_id]);

            // Create new subscription
            $stmt = $pdo->prepare("INSERT INTO Subscriptions (passenger_id, plan_id, start_date, end_date, is_active) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 1)");
            $stmt->execute([$passenger_id, $plan_id]);
            
            $subscription_id = $pdo->lastInsertId();

            // Here you would typically integrate with a payment gateway.
            // For now, we'll simulate a successful subscription and create a pending payment.
            $stmt = $pdo->prepare("SELECT price FROM Subscription_Plans WHERE plan_id = ?");
            $stmt->execute([$plan_id]);
            $plan = $stmt->fetch();

            $stmt = $pdo->prepare("INSERT INTO Payments (user_id, subscription_id, amount, payment_status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$passenger_id, $subscription_id, $plan['price']]);

            $response = ['success' => true, 'message' => 'Subscription successful! Proceed to payment.', 'payment_id' => $pdo->lastInsertId()];
        }
    }
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>