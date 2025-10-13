<?php
// /auth/login.php

session_start();
header('Content-Type: application/json');

require '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    exit;
}

if (!$user['is_active']) {
    echo json_encode(['success' => false, 'message' => 'Account not activated. Please check your email.']);
    exit;
}

// Login successful
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['role'] = $user['role'];
$_SESSION['full_name'] = $user['full_name'];

$redirect_url = '/';
if ($user['role'] === 'passenger') {
    $redirect_url = '../passenger/dashboard.php';
} elseif ($user['role'] === 'driver') {
    $stmt = $pdo->prepare("SELECT license_number, vehicle_details FROM Drivers WHERE driver_id = ?");
    $stmt->execute([$user['user_id']]);
    $driver = $stmt->fetch();

    if (!$driver || $driver['license_number'] === 'PENDING' || $driver['vehicle_details'] === 'PENDING') {
        $redirect_url = '../driver/complete_profile.php';
    } else {
        $redirect_url = '../driver/dashboard.php';
    }
} elseif ($user['role'] === 'admin') {
    $redirect_url = '../admin/dashboard.php';
}

echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => $redirect_url]);
