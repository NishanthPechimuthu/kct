<?php
// /config/config.php

// --- IMPORTANT ---
// Fill in your actual SMTP credentials here, which you provided.
// Do not commit this file with credentials to a public repository.

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'etaxi.ct.ws@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'esdbzhinuabuvfzb'); // Your Gmail App Password
define('SMTP_PORT', 587); // Or 465 for SSL
define('SMTP_SECURE', 'tls'); // or 'ssl'

// The address that emails will be sent from
define('MAIL_FROM_ADDRESS', 'no-reply@etaxi.com');
define('MAIL_FROM_NAME', 'eTaxi Verification');

// The base URL of your application
define('APP_URL', 'http://localhost'); // Adjust if your URL is different

// Per KM charge for rides
define('PER_KM_CHARGE', 10);
