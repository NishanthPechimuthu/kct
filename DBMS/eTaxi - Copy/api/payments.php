<?php
// /api/payments.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$passenger_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$payment_id = $data['payment_id'] ?? null;
$payment_method = $data['method'] ?? null;
$amount = $data['amount'] ?? null;

if (!$payment_id || !$payment_method || !$amount) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payment ID, method, and amount are required.']);
    exit;
}

try {
    // Verify the payment amount and that the payment belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM Payments WHERE payment_id = ? AND user_id = ?");
    $stmt->execute([$payment_id, $passenger_id]);
    $payment = $stmt->fetch();

    if (!$payment) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Payment not found or does not belong to you.']);
        exit;
    }

    // Allow updating the amount if the payment is pending and amount is 0
    if ($payment['payment_status'] === 'pending' && (float)$payment['amount'] === 0.0) {
        // This is the first time the payment is being processed, so we set the amount
        $stmt = $pdo->prepare("UPDATE Payments SET amount = ? WHERE payment_id = ?");
        $stmt->execute([$amount, $payment_id]);
    } elseif ((float)$payment['amount'] !== (float)$amount) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payment amount.']);
        exit;
    }

    // Update the payment status
    $stmt = $pdo->prepare("UPDATE Payments SET payment_status = 'completed', payment_method = ? WHERE payment_id = ?");
    $stmt->execute([$payment_method, $payment_id]);

    echo json_encode(['success' => true, 'message' => 'Payment successful.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
