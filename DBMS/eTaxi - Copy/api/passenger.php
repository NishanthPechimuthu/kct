<?php
// /api/passenger.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$passenger_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'dashboard'; // Default to dashboard action

try {
    if ($action === 'get_wallet_balance') {
        $stmt = $pdo->prepare("SELECT wallet_balance FROM Passengers WHERE passenger_id = ?");
        $stmt->execute([$passenger_id]);
        $balance = $stmt->fetchColumn();
        echo json_encode(['success' => true, 'data' => ['wallet_balance' => $balance]]);

    } elseif ($action === 'dashboard') {
        $response = [
            'success' => true,
            'data' => [
                'subscription' => null,
                'ride_history' => []
            ]
        ];

        $stmt = $pdo->prepare("SELECT sp.plan_name, sp.max_rides, s.rides_taken, s.end_date FROM Subscriptions s JOIN Subscription_Plans sp ON s.plan_id = sp.plan_id WHERE s.passenger_id = ? AND s.is_active = 1 ORDER BY s.end_date DESC LIMIT 1");
        $stmt->execute([$passenger_id]);
        if ($subscription = $stmt->fetch()) {
            $response['data']['subscription'] = $subscription;
        }

        $stmt = $pdo->prepare("SELECT r.ride_id, r.pickup_address, r.dropoff_address, r.fare as ride_fare, p.amount as payment_amount, r.ride_status, r.request_time, r.otp, p.payment_id, p.payment_status FROM Rides r LEFT JOIN Payments p ON r.ride_id = p.ride_id WHERE r.passenger_id = ? ORDER BY r.request_time DESC LIMIT 10");
        $stmt->execute([$passenger_id]);
        $response['data']['ride_history'] = $stmt->fetchAll();

        echo json_encode($response);

    } elseif ($action === 'poll_rides') {
        $ride_ids_str = $_GET['ride_ids'] ?? '';
        if (empty($ride_ids_str)) {
            echo json_encode(['success' => true, 'data' => ['ride_history' => []]]);
            exit;
        }

        $ride_ids = explode(',', $ride_ids_str);
        $placeholders = implode(',', array_fill(0, count($ride_ids), '?'));

        $stmt = $pdo->prepare("SELECT r.ride_id, r.pickup_address, r.dropoff_address, r.fare as ride_fare, p.amount as payment_amount, r.ride_status, r.request_time, r.otp, p.payment_id, p.payment_status FROM Rides r LEFT JOIN Payments p ON r.ride_id = p.ride_id WHERE r.ride_id IN ($placeholders) AND r.passenger_id = ?");
        
        $params = array_merge($ride_ids, [$passenger_id]);
        $stmt->execute($params);
        
        $response['data']['ride_history'] = $stmt->fetchAll();
        echo json_encode($response);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>