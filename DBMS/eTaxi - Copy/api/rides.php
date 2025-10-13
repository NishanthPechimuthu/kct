<?php
// /api/rides.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

// Base fare constants (could be in a config file)
define('BASE_FARE', 50);
define('PER_KM_CHARGE', 12);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$passenger_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

function calculate_distance($lat1, $lon1, $lat2, $lon2) {
    // Simple Haversine formula for distance calculation
    $earth_radius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c; // Distance in KM
}

try {
    if ($action === 'estimate') {
        $distance = calculate_distance($data['pickup']['lat'], $data['pickup']['lng'], $data['dropoff']['lat'], $data['dropoff']['lng']);
        $fare = BASE_FARE + ($distance * PER_KM_CHARGE);

        // Check for active subscription
        $stmt = $pdo->prepare("SELECT s.*, sp.* FROM Subscriptions s JOIN Subscription_Plans sp ON s.plan_id = sp.plan_id WHERE s.passenger_id = ? AND s.is_active = 1");
        $stmt->execute([$passenger_id]);
        $sub = $stmt->fetch();

        if ($sub) {
            if ($sub['rides_taken'] < $sub['max_rides']) {
                if ($distance <= $sub['max_km_per_ride']) {
                    $fare = 0; // Covered by subscription
                } else {
                    $extra_km = $distance - $sub['max_km_per_ride'];
                    $fare = ($extra_km * PER_KM_CHARGE) * ($sub['extra_km_charge_percent'] / 100);
                }
            } else {
                $fare = $fare * ($sub['extra_ride_charge_percent'] / 100);
            }
        }

        echo json_encode(['success' => true, 'data' => ['distance' => round($distance, 2), 'fare' => round($fare, 2)]]);

    } elseif ($action === 'create') {
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO Rides (passenger_id, pickup_location_lat, pickup_location_lng, dropoff_location_lat, dropoff_location_lng, pickup_address, dropoff_address, distance_km, fare, ride_status, otp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'requested', ?)");
        $stmt->execute([$passenger_id, $data['pickup']['lat'], $data['pickup']['lng'], $data['dropoff']['lat'], $data['dropoff']['lng'], $data['pickup_address'], $data['dropoff_address'], $data['rideDetails']['distance'], $data['rideDetails']['fare'], $otp]);
        $ride_id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Ride requested successfully!', 'ride_id' => $ride_id]);

    } elseif ($action === 'cancel_by_passenger') {
        $ride_id = $data['ride_id'] ?? null;

        if (!$ride_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ride ID is required.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM Rides WHERE ride_id = ? AND passenger_id = ?");
        $stmt->execute([$ride_id, $passenger_id]);
        $ride = $stmt->fetch();

        if (!$ride) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ride not found or you are not authorized to cancel it.']);
            exit;
        }

        if ($ride['ride_status'] !== 'requested') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ride cannot be cancelled at this stage.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE Rides SET ride_status = 'cancelled_by_passenger' WHERE ride_id = ?");
        $stmt->execute([$ride_id]);

        echo json_encode(['success' => true, 'message' => 'Ride cancelled successfully.']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>