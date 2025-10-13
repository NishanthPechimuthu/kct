<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav x-data="{ isOpen: false }" class="bg-white shadow-md fixed w-full z-50 top-0">
    <div class="container mx-auto px-6 py-3 flex justify-between items-center">
        <a class="text-2xl font-bold text-gray-800" href="/">
            <i class="fas fa-taxi"></i> eTaxi
        </a>
        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-6 items-center">
            <a href="/passenger/dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'text-blue-500 font-bold' : 'text-gray-600 hover:text-blue-500' ?>">Dashboard</a>
            <a href="/passenger/book_ride.php" class="<?= ($current_page == 'book_ride.php') ? 'text-blue-500 font-bold' : 'text-gray-600 hover:text-blue-500' ?>">Book a Ride</a>
            <a href="/passenger/subscription.php" class="<?= ($current_page == 'subscription.php') ? 'text-blue-500 font-bold' : 'text-gray-600 hover:text-blue-500' ?>">Subscription</a>
            <a href="/auth/logout.php" class="text-gray-600 hover:text-blue-500">Logout</a>
        </div>
        <!-- Mobile Menu Button -->
        <div class="md:hidden">
            <button @click="isOpen = !isOpen" class="text-gray-800 focus:outline-none">
                <i :class="isOpen ? 'fas fa-times' : 'fas fa-bars'" class="h-6 w-6"></i>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         @click.away="isOpen = false" class="md:hidden bg-white border-t">
        <a href="/passenger/dashboard.php" class="block py-2 px-4 text-sm <?= ($current_page == 'dashboard.php') ? 'font-bold text-blue-500 hover:bg-gray-200' : 'hover:bg-gray-200' ?>">Dashboard</a>
        <a href="/passenger/book_ride.php" class="block py-2 px-4 text-sm <?= ($current_page == 'book_ride.php') ? 'font-bold text-blue-500 hover:bg-gray-200' : 'hover:bg-gray-200' ?>">Book a Ride</a>
        <a href="/passenger/subscription.php" class="block py-2 px-4 text-sm <?= ($current_page == 'subscription.php') ? 'font-bold text-blue-500 hover:bg-gray-200' : 'hover:bg-gray-200' ?>">Subscription</a>
        <a href="/" class="block py-2 px-4 text-sm hover:bg-gray-200">Logout</a>
    </div>
</nav>
