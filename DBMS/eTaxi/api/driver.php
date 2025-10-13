<?php
// /api/driver.php
session_start();
header('Content-Type: application/json');

require '../config/db.php';
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$driver_id = $_SESSION['user_id'];

// Support both JSON and form requests
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $data['action'] ?? $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_ride_requests':
            $stmt = $pdo->prepare("SELECT r.*, u.full_name AS passenger_name 
                FROM Rides r 
                JOIN Users u ON r.passenger_id = u.user_id 
                WHERE r.ride_status = 'requested'");
            $stmt->execute();
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'accept_ride':
            $ride_id = $data['ride_id'] ?? null;
            if (!$ride_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ride ID is required.']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM Rides WHERE ride_id = ? AND ride_status = 'requested'");
            $stmt->execute([$ride_id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ride not available or already accepted.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE Rides SET driver_id = ?, ride_status = 'accepted', start_time = CURRENT_TIMESTAMP WHERE ride_id = ?");
            $stmt->execute([$driver_id, $ride_id]);
            echo json_encode(['success' => true, 'message' => 'Ride accepted successfully.']);
            break;

        case 'get_ride_details':
            $ride_id = $_GET['ride_id'] ?? null;
            if (!$ride_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ride ID is required.']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT r.*, u.full_name AS passenger_name 
                FROM Rides r 
                JOIN Users u ON r.passenger_id = u.user_id 
                WHERE r.ride_id = ? AND r.driver_id = ?");
            $stmt->execute([$ride_id, $driver_id]);
            $ride = $stmt->fetch();
            if (!$ride) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ride not found or not assigned to you.']);
                exit;
            }
            echo json_encode(['success' => true, 'data' => $ride]);
            break;

        case 'get_maintenance_history':
            $stmt = $pdo->prepare("SELECT * FROM Driver_Maintenance WHERE driver_id = ? ORDER BY upload_date DESC");
            $stmt->execute([$driver_id]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'upload_maintenance_document':
            $document_type = $_POST['document_type'] ?? null;
            $next_due_date = $_POST['next_due_date'] ?? null;
            $file = $_FILES['document_file'] ?? null;
            if (!$document_type || !$next_due_date || !$file) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                exit;
            }
            $upload_dir = '../data/maintenance_documents/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('doc_', true) . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $stmt = $pdo->prepare("INSERT INTO Driver_Maintenance (driver_id, document_type, document_path, upload_date, next_due_date) VALUES (?, ?, ?, CURDATE(), ?)");
                $stmt->execute([$driver_id, $document_type, $file_path, $next_due_date]);
                echo json_encode(['success' => true, 'message' => 'Document uploaded successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to upload document.']);
            }
            break;

        case 'get_dashboard_data':
            // total earnings
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(fare),0) FROM Rides WHERE driver_id = ? AND ride_status = 'completed'");
            $stmt->execute([$driver_id]);
            $total_earnings = $stmt->fetchColumn();

            // completed rides
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Rides WHERE driver_id = ? AND ride_status = 'completed'");
            $stmt->execute([$driver_id]);
            $completed_rides = $stmt->fetchColumn();

            // driver rating
            $stmt = $pdo->prepare("SELECT rating FROM Drivers WHERE driver_id = ?");
            $stmt->execute([$driver_id]);
            $rating = $stmt->fetchColumn() ?: 'N/A';

            // assigned ride
            $stmt = $pdo->prepare("SELECT * FROM Rides WHERE driver_id = ? AND ride_status IN ('accepted','in_progress') LIMIT 1");
            $stmt->execute([$driver_id]);
            $assigned_ride = $stmt->fetch();

            // ride history
            $stmt = $pdo->prepare("SELECT * FROM Rides WHERE driver_id = ? AND ride_status IN ('completed','cancelled_by_driver','cancelled_by_passenger') ORDER BY end_time DESC LIMIT 10");
            $stmt->execute([$driver_id]);
            $ride_history = $stmt->fetchAll();

            // last maintenance
            $stmt = $pdo->prepare("SELECT * FROM Driver_Maintenance WHERE driver_id = ? ORDER BY upload_date DESC LIMIT 1");
            $stmt->execute([$driver_id]);
            $last_maintenance = $stmt->fetch() ?: null;

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_earnings' => round($total_earnings, 2),
                    'completed_rides' => $completed_rides,
                    'rating' => $rating,
                    'assigned_ride' => $assigned_ride,
                    'ride_history' => $ride_history,
                    'last_maintenance' => $last_maintenance
                ]
            ]);
            break;

        case 'verify_otp_and_start_ride':
            $ride_id = $data['ride_id'] ?? null;
            $otp = $data['otp'] ?? null;
            if (!$ride_id || !$otp) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ride ID and OTP are required.']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM Rides WHERE ride_id = ? AND driver_id = ?");
            $stmt->execute([$ride_id, $driver_id]);
            $ride = $stmt->fetch();
            if (!$ride) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ride not found or not assigned to you.']);
                exit;
            }
            if ($ride['otp'] !== $otp) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE Rides SET ride_status = 'in_progress' WHERE ride_id = ?");
            $stmt->execute([$ride_id]);
            $transaction_id = uniqid('txn_');
            $stmt = $pdo->prepare("INSERT INTO Payments (user_id, ride_id, amount, payment_status, transaction_id) VALUES (?, ?, 0, 'pending', ?)");
            $stmt->execute([$ride['passenger_id'], $ride_id, $transaction_id]);
            echo json_encode(['success' => true, 'message' => 'Ride started successfully.']);
            break;

        case 'complete_ride':
            $ride_id = $data['ride_id'] ?? null;
            if (!$ride_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ride ID is required.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE Rides SET ride_status = 'completed', end_time = CURRENT_TIMESTAMP WHERE ride_id = ? AND driver_id = ?");
            $stmt->execute([$ride_id, $driver_id]);
            // Get ride details to update payment and subscription
            $stmt = $pdo->prepare("SELECT passenger_id, fare FROM Rides WHERE ride_id = ?");
            $stmt->execute([$ride_id]);
            $ride = $stmt->fetch();

            if ($ride) {
                // Update payment record
                $stmt = $pdo->prepare("UPDATE Payments SET amount = ?, payment_status = 'completed' WHERE ride_id = ?");
                $stmt->execute([$ride['fare'], $ride_id]);

                // If the ride was covered by a subscription (fare is 0), increment the usage count
                if ((float)$ride['fare'] === 0.0) {
                    $update_sub_stmt = $pdo->prepare(
                        "UPDATE Subscriptions SET rides_taken = rides_taken + 1 
                         WHERE passenger_id = ? AND is_active = 1 AND CURDATE() BETWEEN start_date AND end_date"
                    );
                    $update_sub_stmt->execute([$ride['passenger_id']]);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Ride completed successfully.']);
            break;

        case 'update_profile':
            $license_number = $data['license_number'] ?? null;
            $vehicle_details = $data['vehicle_details'] ?? null;
            if (!$license_number || !$vehicle_details) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'License number and vehicle details are required.']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE Drivers SET license_number = ?, vehicle_details = ? WHERE driver_id = ?");
            $stmt->execute([$license_number, $vehicle_details, $driver_id]);
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
