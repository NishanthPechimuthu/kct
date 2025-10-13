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
    <title>Book a Ride - eTaxi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 400px; cursor: crosshair; }
        .suggestions-container { position: relative; }
        .suggestions-list { 
            position: absolute; 
            background-color: white; 
            border: 1px solid #dbdbdb; 
            border-top: none; 
            width: 100%; 
            max-height: 150px; 
            overflow-y: auto; 
            z-index: 1000; 
        }
        .suggestion-item { padding: 8px 12px; cursor: pointer; }
        .suggestion-item:hover { background-color: #f0f0f0; }
        #loader { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>
    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold mb-8">Book Your Ride</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-lg">
                <div id="location-error" class="hidden bg-red-100 text-red-700 p-3 rounded-md mb-4"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="suggestions-container">
                        <label for="pickup-address" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                        <input type="text" id="pickup-address" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="Getting your location...">
                        <div id="pickup-suggestions" class="suggestions-list hidden"></div>
                    </div>
                    <div class="suggestions-container">
                        <label for="dropoff-address" class="block text-sm font-medium text-gray-700">Drop-off Location</label>
                        <input type="text" id="dropoff-address" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm" placeholder="Type to search...">
                        <div id="dropoff-suggestions" class="suggestions-list hidden"></div>
                    </div>
                </div>
                <div class="relative">
                    <div id="map" class="rounded-lg"></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Ride Details</h2>
                <div id="ride-details">
                    <div class="mb-4">
                        <p class="text-gray-600">Estimated Distance</p>
                        <p id="distance" class="text-2xl font-bold">-- KM</p>
                    </div>
                    <div class="mb-6">
                        <p class="text-gray-600">Estimated Fare</p>
                        <p id="fare" class="text-2xl font-bold">₹--</p>
                    </div>
                    <button id="book-ride-btn" class="w-full bg-gray-400 text-white font-bold py-3 px-4 rounded-lg cursor-not-allowed" disabled>Confirm Locations to Book</button>
                </div>
                <div id="booking-status" class="mt-4"></div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- STATE & CONSTANTS ---
            const state = { pickup: null, dropoff: null, rideDetails: null };
            const locations = [
                // Existing Coimbatore locations
                { name: 'Coimbatore International Airport (CJB)', lat: 11.0344, lng: 77.0434 },
                { name: 'Coimbatore Junction Railway Station', lat: 11.0251, lng: 76.9658 },
                { name: 'Marudhamalai Temple', lat: 10.9072, lng: 76.6867 },
                { name: 'Isha Yoga Center', lat: 10.9419, lng: 76.8263 },
                { name: 'Perur Pateeswarar Temple', lat: 11.0100, lng: 76.9342 },
                { name: 'Dhyanalinga Temple', lat: 10.9419, lng: 76.8263 },
                { name: 'Annapoorna Sree Abhirami Temple', lat: 11.0000, lng: 76.9686 },
                { name: 'Siruvani Waterfalls', lat: 11.0714, lng: 76.7153 },
                { name: 'Monkey Falls', lat: 10.5847, lng: 77.0097 },
                { name: 'Gass Forest Museum', lat: 11.0042, lng: 76.9683 },
                { name: 'Tamil Nadu Agricultural University', lat: 11.0219, lng: 76.9314 },
                { name: 'KG Choultry', lat: 11.0000, lng: 76.9686 },
                // New locations added based on user request (Coimbatore and Pollachi focus)
                { name: 'Kumaraguru College of Technology', lat: 11.0778, lng: 76.9896 },
                { name: 'Nachimuthu Polytechnic College, Pollachi', lat: 10.6528, lng: 77.0053 },
                { name: 'Pollachi', lat: 10.6573, lng: 77.0107 },
                { name: 'Pollachi Bus Stand', lat: 10.6614, lng: 77.0064 },
                { name: 'Gandhipuram Bus Stand', lat: 11.0205, lng: 76.9667 },
                { name: 'Dr. Mahalingam College of Engineering and Technology (MCET), Pollachi', lat: 10.6786, lng: 77.0003 },
                { name: 'Coimbatore Institute of Technology (CIT)', lat: 11.0236, lng: 76.9436 },
                { name: 'Sri Krishna College of Engineering and Technology (SKCET)', lat: 10.9458, lng: 76.9172 }
            ];

            // --- DOM ELEMENTS ---
            const dom = {
                map: L.map('map'),
                pickupInput: document.getElementById('pickup-address'),
                dropoffInput: document.getElementById('dropoff-address'),
                pickupSuggestions: document.getElementById('pickup-suggestions'),
                dropoffSuggestions: document.getElementById('dropoff-suggestions'),
                distance: document.getElementById('distance'),
                fare: document.getElementById('fare'),
                bookBtn: document.getElementById('book-ride-btn'),
                bookingStatus: document.getElementById('booking-status'),
                locationError: document.getElementById('location-error')
            };

            // --- MAP INITIALIZATION ---
            const map = dom.map.setView([11.0168, 76.9558], 11); // Default to Coimbatore
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            const pickupMarker = L.marker([11.0168, 76.9558], { draggable: true, autoPan: true }).addTo(map).bindPopup('Pickup');
            const dropoffMarker = L.marker([11.0344, 77.0434], { draggable: true, autoPan: true }).addTo(map).bindPopup('Drop-off'); // Default to Airport

            // --- FUNCTIONS ---
            const calculateDistance = (lat1, lon1, lat2, lon2) => {
                const R = 6371; // Earth's radius in kilometers
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLon / 2) * Math.sin(dLon / 2);
                return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
            };

            const updateRideDetails = async () => {
                if (!state.pickup || !state.dropoff) return;
                dom.bookBtn.textContent = 'Calculating Fare...';
                dom.bookBtn.disabled = true;

                try {
                    const response = await axios.post('/api/rides.php', { 
                        action: 'estimate', 
                        pickup: state.pickup, 
                        dropoff: state.dropoff 
                    });
                    if (response.data.success) {
                        state.rideDetails = response.data.data;
                        dom.distance.textContent = `${state.rideDetails.distance} KM`;
                        dom.fare.textContent = `₹${state.rideDetails.fare}`;
                        dom.bookBtn.textContent = 'Book Ride';
                        dom.bookBtn.disabled = false;
                        dom.bookBtn.classList.replace('bg-gray-400', 'bg-blue-500');
                        dom.bookBtn.classList.remove('cursor-not-allowed');
                    } else {
                        throw new Error('API response unsuccessful');
                    }
                } catch (e) {
                    dom.bookingStatus.innerHTML = `<p class="text-red-500">Fare estimation failed: ${e.message}</p>`;
                    dom.bookBtn.textContent = 'Book Ride';
                    dom.bookBtn.disabled = true;
                }
            };

            const showSuggestions = (input, suggestionsEl) => {
                const term = input.value.toLowerCase().trim();
                suggestionsEl.innerHTML = ''; // Clear previous suggestions
                suggestionsEl.classList.add('hidden'); // Hide by default

                if (!term) {
                    return; // Exit if input is empty
                }

                // Create a copy of locations to avoid modifying the original
                const sortedLocations = [...locations].map(loc => ({
                    ...loc,
                    distance: state.pickup ? calculateDistance(state.pickup.lat, state.pickup.lng, loc.lat, loc.lng) : Infinity
                }));

                // Sort by distance if pickup is set, otherwise keep original order
                if (state.pickup) {
                    sortedLocations.sort((a, b) => a.distance - b.distance);
                }

                // Filter locations based on search term
                const filtered = sortedLocations.filter(loc => loc.name.toLowerCase().includes(term));

                if (filtered.length > 0) {
                    filtered.forEach(loc => {
                        const item = document.createElement('div');
                        item.className = 'suggestion-item';
                        item.textContent = loc.name;
                        item.onclick = () => {
                            input.value = loc.name;
                            suggestionsEl.classList.add('hidden');
                            const marker = (input === dom.pickupInput) ? pickupMarker : dropoffMarker;
                            marker.setLatLng([loc.lat, loc.lng]);
                            // Update state
                            if (input === dom.pickupInput) {
                                state.pickup = { lat: loc.lat, lng: loc.lng };
                            } else {
                                state.dropoff = { lat: loc.lat, lng: loc.lng };
                            }
                            updateRideDetails();
                        };
                        suggestionsEl.appendChild(item);
                    });
                    suggestionsEl.classList.remove('hidden');
                } else {
                    // Show a "No results" message
                    const item = document.createElement('div');
                    item.className = 'suggestion-item text-gray-500';
                    item.textContent = 'No matching locations found';
                    suggestionsEl.appendChild(item);
                    suggestionsEl.classList.remove('hidden');
                }
            };

            // --- EVENT LISTENERS ---
            dom.pickupInput.addEventListener('input', () => showSuggestions(dom.pickupInput, dom.pickupSuggestions));
            dom.dropoffInput.addEventListener('input', () => showSuggestions(dom.dropoffInput, dom.dropoffSuggestions));
            document.addEventListener('click', e => {
                if (!e.target.closest('.suggestions-container')) {
                    dom.pickupSuggestions.classList.add('hidden');
                    dom.dropoffSuggestions.classList.add('hidden');
                }
            });

            pickupMarker.on('dragend', e => {
                state.pickup = e.target.getLatLng();
                dom.pickupInput.value = `Custom Location (${state.pickup.lat.toFixed(3)}, ${state.pickup.lng.toFixed(3)})`;
                updateRideDetails();
            });

            dropoffMarker.on('dragend', e => {
                state.dropoff = e.target.getLatLng();
                dom.dropoffInput.value = `Custom Location (${state.dropoff.lat.toFixed(3)}, ${state.dropoff.lng.toFixed(3)})`;
                updateRideDetails();
            });

            dom.bookBtn.addEventListener('click', async () => {
                if (!state.rideDetails) return;
                try {
                    const response = await axios.post('/api/rides.php', { 
                        action: 'create', 
                        ...state, 
                        pickup_address: dom.pickupInput.value, 
                        dropoff_address: dom.dropoffInput.value 
                    });
                    if (response.data.success) {
                        dom.bookingStatus.innerHTML = `<p class="text-green-500 font-bold">Ride booked! ID: ${response.data.ride_id}</p>`;
                        dom.bookBtn.disabled = true;
                    } else {
                        throw new Error('API response unsuccessful');
                    }
                } catch (e) {
                    dom.bookingStatus.innerHTML = `<p class="text-red-500">Booking failed: ${e.message}</p>`;
                }
            });

            // --- INITIALIZATION ---
            const handleGeoSuccess = (pos) => {
                const coords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                state.pickup = coords;
                map.setView(coords, 14);
                pickupMarker.setLatLng(coords);
                dom.pickupInput.value = "Your Current Location";
                dom.dropoffInput.placeholder = "Where to?";
                updateRideDetails();
            };

            const handleGeoError = (err) => {
                dom.locationError.classList.remove('hidden');
                dom.locationError.textContent = `Could not get location: ${err.message}. Please type an address manually.`;
                if (window.location.protocol !== 'https:') {
                    dom.locationError.textContent += ' Location features work best on secure (https) sites.';
                }
                dom.pickupInput.placeholder = "Type pickup address";
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(handleGeoSuccess, handleGeoError, { timeout: 10000 });
            } else {
                handleGeoError({ message: "Geolocation is not supported by this browser." });
            }

            // Set initial state from markers
            state.pickup = pickupMarker.getLatLng();
            state.dropoff = dropoffMarker.getLatLng();
            updateRideDetails();
        });
    </script>
</body>
</html>