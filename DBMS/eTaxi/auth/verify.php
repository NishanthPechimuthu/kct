<?php
// /auth/verify.php

require '../config/db.php';

if (empty($_GET['email']) || empty($_GET['token'])) {
    die('<h1 style="color:red;text-align:center;">Invalid verification link.</h1>');
}

$email = $_GET['email'];
$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? AND verification_token = ?");
$stmt->execute([$email, $token]);
$user = $stmt->fetch();

if (!$user) {
    die('<h1 style="color:red;text-align:center;">Invalid verification link or account already activated.</h1>');
}

// Activate account
$stmt = $pdo->prepare("UPDATE Users SET is_active = 1, verification_token = NULL WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Activated - eTaxi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-xl p-10 max-w-lg text-center">
    <div class="text-green-500 text-5xl mb-4">
      <i class="fas fa-check-circle"></i>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Account Activated!</h1>
    <p class="text-gray-600 mb-6">
      Your account has been successfully activated. You can now log in and start using 
      <span class="font-semibold text-blue-500">eTaxi</span>.
    </p>
    
    <a href="/login.html" 
       class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
      <i class="fas fa-sign-in-alt"></i> Go to Login Page
    </a>
  </div>

</body>
</html>
