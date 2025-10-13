<?php
// /auth/register.php

header('Content-Type: application/json');

require '../config/db.php';
require '../config/config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

// Basic validation
if (empty($data['fullName']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$fullName = $data['fullName'];
$email = $data['email'];
$password = $data['password'];
$role = $data['role'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Check if user already exists
$stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$verification_token = bin2hex(random_bytes(32));

$pdo->beginTransaction();

try {
    // Insert into Users table
    $stmt = $pdo->prepare("INSERT INTO Users (full_name, email, password_hash, role, verification_token, is_active) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$fullName, $email, $password_hash, $role, $verification_token]);
    $user_id = $pdo->lastInsertId();

    // Insert into role-specific table
    if ($role === 'passenger') {
        $stmt = $pdo->prepare("INSERT INTO Passengers (passenger_id) VALUES (?)");
        $stmt->execute([$user_id]);
    } elseif ($role === 'driver') {
        // For drivers, you might require more info at registration.
        // This is a simplified example.
        $stmt = $pdo->prepare("INSERT INTO Drivers (driver_id, license_number, vehicle_details) VALUES (?, 'PENDING', 'PENDING')");
        $stmt->execute([$user_id]);
    }

    // Send verification email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($email, $fullName);

    $mail->isHTML(true);
    $mail->Subject = 'Activate Your eTaxi Account';
    $verification_link = APP_URL . '/auth/verify.php?email=' . urlencode($email) . '&token=' . $verification_token;
    $mail->Body    = 'Hi ' . $fullName . ',<br><br>Thanks for registering! Please click the link below to activate your account:<br><a href="' . $verification_link . '">' . $verification_link . '</a><br><br>Thanks,<br>The eTaxi Team';

    $mail->send();

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Registration successful! Please check your email to activate your account.']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}
