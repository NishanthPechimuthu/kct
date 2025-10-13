<?php
session_start();

// Check if the user is logged in and is a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'passenger') {
    // If not logged in or not a passenger, redirect to the login page
    header('Location: /login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
    <main class="container mx-auto px-6 py-24">
        <div id="payment-container" class="w-full max-w-lg bg-white rounded-lg shadow-md p-8 mx-auto">
            <h1 class="text-3xl font-bold text-center">Complete Your Payment</h1>
            <p id="payment-description" class="text-gray-600 text-center">Loading...</p>
            <p class="text-center text-gray-600">Amount to Pay: <span id="payment-amount" class="font-bold text-2xl text-blue-600">₹--</span></p>

            <form id="payment-form" class="mt-8" x-data="{ method: 'card' }">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Choose Payment Method</label>
                        <div class="mt-2 flex justify-around bg-gray-100 rounded-lg p-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="card" class="form-radio" x-model="method" checked>
                                <span>Card</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="upi" class="form-radio" x-model="method">
                                <span>UPI</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="wallet" class="form-radio" x-model="method">
                                <span>Wallet</span>
                            </label>
                        </div>
                    </div>

                    <!-- Card Details Form -->
                    <div x-show="method === 'card'" class="space-y-4 border-t pt-4">
                        <div>
                            <label for="card-number" class="block text-sm font-medium text-gray-700">Card Number</label>
                            <input type="text" id="card-number" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="expiry-month" class="block text-sm font-medium text-gray-700">Expiry</label>
                                <input type="text" id="expiry-month" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="MM/YY">
                            </div>
                            <div>
                                <label for="cvc" class="block text-sm font-medium text-gray-700">CVC</label>
                                <input type="text" id="cvc" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="CVC">
                            </div>
                        </div>
                    </div>

                    <!-- UPI Details Form -->
                    <div x-show="method === 'upi'" class="space-y-4 border-t pt-4">
                        <div>
                            <label for="upi-id" class="block text-sm font-medium text-gray-700">UPI ID</label>
                            <input type="text" id="upi-id" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="yourname@bank">
                        </div>
                    </div>

                    <!-- Wallet Details -->
                    <div x-show="method === 'wallet'" class="space-y-4 border-t pt-4 text-center">
                        <p class="text-gray-700">Your Wallet Balance</p>
                        <p class="text-2xl font-bold">₹500.00</p>
                        <p class="text-sm text-gray-500">(Sufficient balance for this transaction)</p>
                    </div>

                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-600">Pay Now</button>
                </div>
            </form>

            <div id="payment-status" class="mt-4 text-center"></div>

            <div id="loading-spinner" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                <i class="fas fa-spinner fa-spin fa-3x text-blue-500"></i>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="/passenger/payment.js"></script>
</body>
</html>