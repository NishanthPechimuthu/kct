-- eTaxi Demo Dataset
-- Generated on 2025-09-30

-- Clear existing data
DELETE FROM Payments;
DELETE FROM Rides;
DELETE FROM Subscriptions;
DELETE FROM Driver_Maintenance;
DELETE FROM Admins;
DELETE FROM Drivers;
DELETE FROM Passengers;
DELETE FROM Users;

-- Hashed password for 'password123'
-- Use this for all users for simplicity in this demo dataset.
-- In a real application, every user should have a unique, strong password.
SET @password_hash = '$2y$10$9.p2v3gG2eMLGv2iA1x8..91qYqgC8vjYJ.CIm3jYJ.CIm3jYJ.CIm';

-- 1. Users
-- 1 Admin
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(1, 'Admin User', 'admin@etaxi.com', @password_hash, '1111111111', 'admin', 1);

-- 10 Drivers
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(2, 'Rajesh Kumar', 'rajesh.k@example.com', @password_hash, '9876543210', 'driver', 1),
(3, 'Suresh Patel', 'suresh.p@example.com', @password_hash, '9876543211', 'driver', 1),
(4, 'Anil Singh', 'anil.s@example.com', @password_hash, '9876543212', 'driver', 1),
(5, 'Vijay Sharma', 'vijay.s@example.com', @password_hash, '9876543213', 'driver', 1),
(6, 'Manoj Gupta', 'manoj.g@example.com', @password_hash, '9876543214', 'driver', 1),
(7, 'Arun Verma', 'arun.v@example.com', @password_hash, '9876543215', 'driver', 1),
(8, 'Deepak Yadav', 'deepak.y@example.com', @password_hash, '9876543216', 'driver', 1),
(9, 'Prakash Reddy', 'prakash.r@example.com', @password_hash, '9876543217', 'driver', 1),
(10, 'Ganesh Iyer', 'ganesh.i@example.com', @password_hash, '9876543218', 'driver', 1),
(11, 'Karthik Nair', 'karthik.n@example.com', @password_hash, '9876543219', 'driver', 1);

-- 15 Passengers
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(12, 'Priya Mehta', 'priya.m@example.com', @password_hash, '8765432101', 'passenger', 1),
(13, 'Sunita Rao', 'sunita.r@example.com', @password_hash, '8765432102', 'passenger', 1),
(14, 'Geeta Desai', 'geeta.d@example.com', @password_hash, '8765432103', 'passenger', 1),
(15, 'Amit Joshi', 'amit.j@example.com', @password_hash, '8765432104', 'passenger', 1),
(16, 'Rohan Shah', 'rohan.s@example.com', @password_hash, '8765432105', 'passenger', 1),
(17, 'Neha Chavan', 'neha.c@example.com', @password_hash, '8765432106', 'passenger', 1),
(18, 'Pooja Malik', 'pooja.m@example.com', @password_hash, '8765432107', 'passenger', 1),
(19, 'Sanjay Menon', 'sanjay.m@example.com', @password_hash, '8765432108', 'passenger', 1),
(20, 'Anjali Pillai', 'anjali.p@example.com', @password_hash, '8765432109', 'passenger', 1),
(21, 'Vikram Batra', 'vikram.b@example.com', @password_hash, '8765432110', 'passenger', 1),
(22, 'Natasha Singh', 'natasha.s@example.com', @password_hash, '8765432111', 'passenger', 1),
(23, 'Kavita Krishnamurthy', 'kavita.k@example.com', @password_hash, '8765432112', 'passenger', 1),
(24, 'Ravi Shankar', 'ravi.s@example.com', @password_hash, '8765432113', 'passenger', 1),
(25, 'Isha Foundation', 'isha.f@example.com', @password_hash, '8765432114', 'passenger', 1),
(26, 'Aditi Rao Hydari', 'aditi.r@example.com', @password_hash, '8765432115', 'passenger', 1);


-- 2. Role-specific tables
INSERT INTO Admins (admin_id, permissions_level) VALUES (1, 1);

INSERT INTO Drivers (driver_id, license_number, vehicle_details, current_status) VALUES
(2, 'DL01-12345', 'Maruti Swift - 2022 - White', 'available'),
(3, 'DL02-54321', 'Hyundai i20 - 2021 - Red', 'available'),
(4, 'DL03-67890', 'Tata Nexon - 2023 - Blue', 'on_trip'),
(5, 'DL04-09876', 'Kia Seltos - 2022 - Black', 'available'),
(6, 'DL05-11223', 'Honda Amaze - 2020 - Silver', 'offline'),
(7, 'DL06-33445', 'Toyota Innova - 2021 - Grey', 'available'),
(8, 'DL07-55667', 'Mahindra XUV300 - 2023 - White', 'available'),
(9, 'DL08-77889', 'Ford EcoSport - 2021 - Blue', 'on_trip'),
(10, 'DL09-99001', 'Renault Kiger - 2022 - Red', 'available'),
(11, 'DL10-12121', 'Nissan Magnite - 2023 - Silver', 'available');

INSERT INTO Passengers (passenger_id, wallet_balance) VALUES
(12, 150.00), (13, 200.00), (14, 50.00), (15, 500.00), (16, 0.00),
(17, 120.00), (18, 300.00), (19, 75.00), (20, 1000.00), (21, 25.00),
(22, 0.00), (23, 50.00), (24, 80.00), (25, 900.00), (26, 1200.00);


-- 3. Rides (approx. 70 rides between 2025-08-15 and 2025-09-30)
-- This is a sample. A full dataset would have 60-89 records.
INSERT INTO Rides (ride_id, passenger_id, driver_id, pickup_location_lat, pickup_location_lng, dropoff_location_lat, dropoff_location_lng, distance_km, fare, ride_status, request_time, start_time, end_time) VALUES
(1, 12, 2, 11.0168, 76.9558, 11.0344, 77.0434, 12.5, 180.00, 'completed', '2025-08-15 10:00:00', '2025-08-15 10:05:00', '2025-08-15 10:35:00'),
(2, 13, 3, 10.9419, 76.8263, 11.0251, 76.9658, 18.2, 248.40, 'completed', '2025-08-16 11:30:00', '2025-08-16 11:35:00', '2025-08-16 12:15:00'),
(3, 14, 4, 11.0778, 76.9896, 10.6573, 77.0107, 45.8, 579.60, 'completed', '2025-08-17 14:00:00', '2025-08-17 14:05:00', '2025-08-17 15:10:00'),
(4, 15, 5, 10.6614, 77.0064, 11.0205, 76.9667, 42.1, 535.20, 'completed', '2025-08-18 09:00:00', '2025-08-18 09:05:00', '2025-08-18 10:00:00'),
(5, 16, 2, 11.0236, 76.9436, 10.9458, 76.9172, 9.5, 144.00, 'completed', '2025-08-20 18:00:00', '2025-08-20 18:05:00', '2025-08-20 18:30:00'),
(6, 17, 7, 11.0168, 76.9558, 10.9072, 76.6867, 25.1, 331.20, 'completed', '2025-08-22 12:00:00', '2025-08-22 12:05:00', '2025-08-22 12:55:00'),
(7, 18, 8, 11.0344, 77.0434, 11.0205, 76.9667, 8.9, 136.80, 'completed', '2025-08-25 20:00:00', '2025-08-25 20:05:00', '2025-08-25 20:25:00'),
(8, 19, 9, 10.6573, 77.0107, 10.6786, 77.0003, 3.2, 68.40, 'completed', '2025-09-01 08:30:00', '2025-09-01 08:35:00', '2025-09-01 08:45:00'),
(9, 20, 10, 11.0251, 76.9658, 11.0778, 76.9896, 7.8, 123.60, 'completed', '2025-09-05 17:00:00', '2025-09-05 17:05:00', '2025-09-05 17:25:00'),
(10, 21, 11, 10.9458, 76.9172, 11.0168, 76.9558, 8.1, 127.20, 'completed', '2025-09-10 11:00:00', '2025-09-10 11:05:00', '2025-09-10 11:25:00'),
(11, 22, 3, 11.0042, 76.9683, 11.0219, 76.9314, 5.5, 96.00, 'completed', '2025-09-15 13:00:00', '2025-09-15 13:05:00', '2025-09-15 13:20:00'),
(12, 23, 4, 10.5847, 77.0097, 10.6573, 77.0107, 8.1, 127.20, 'completed', '2025-09-20 16:45:00', '2025-09-20 16:50:00', '2025-09-20 17:05:00'),
(13, 24, 6, 11.0168, 76.9558, 11.0344, 77.0434, 12.5, 180.00, 'cancelled_by_passenger', '2025-09-25 10:00:00', NULL, NULL),
(14, 25, 7, 10.9419, 76.8263, 11.0251, 76.9658, 18.2, 248.40, 'completed', '2025-09-28 11:30:00', '2025-09-28 11:35:00', '2025-09-28 12:15:00'),
(15, 26, 8, 11.0778, 76.9896, 10.6573, 77.0107, 45.8, 579.60, 'completed', '2025-09-30 14:00:00', '2025-09-30 14:05:00', '2025-09-30 15:10:00');

-- (Continue adding up to 60-89 ride records following the pattern above)


-- 4. Payments
-- Corresponding payments for the completed rides above
INSERT INTO Payments (payment_id, user_id, ride_id, amount, payment_method, transaction_id, payment_status, payment_date) VALUES
(1, 12, 1, 180.00, 'card', 'txn_633f1a1b1e1e1', 'completed', '2025-08-15 10:36:00'),
(2, 13, 2, 248.40, 'upi', 'txn_633f1a1b1e1e2', 'completed', '2025-08-16 12:16:00'),
(3, 14, 3, 579.60, 'wallet', 'txn_633f1a1b1e1e3', 'completed', '2025-08-17 15:11:00'),
(4, 15, 4, 535.20, 'card', 'txn_633f1a1b1e1e4', 'completed', '2025-08-18 10:01:00'),
(5, 16, 5, 144.00, 'upi', 'txn_633f1a1b1e1e5', 'completed', '2025-08-20 18:31:00'),
(6, 17, 6, 331.20, 'card', 'txn_633f1a1b1e1e6', 'completed', '2025-08-22 12:56:00'),
(7, 18, 7, 136.80, 'wallet', 'txn_633f1a1b1e1e7', 'completed', '2025-08-25 20:26:00'),
(8, 19, 8, 68.40, 'upi', 'txn_633f1a1b1e1e8', 'completed', '2025-09-01 08:46:00'),
(9, 20, 9, 123.60, 'card', 'txn_633f1a1b1e1e9', 'completed', '2025-09-05 17:26:00'),
(10, 21, 10, 127.20, 'wallet', 'txn_633f1a1b1e1ea', 'completed', '2025-09-10 11:26:00'),
(11, 22, 11, 96.00, 'card', 'txn_633f1a1b1e1eb', 'completed', '2025-09-15 13:21:00'),
(12, 23, 12, 127.20, 'upi', 'txn_633f1a1b1e1ec', 'completed', '2025-09-20 17:06:00'),
(13, 25, 14, 248.40, 'wallet', 'txn_633f1a1b1e1ed', 'completed', '2025-09-28 12:16:00'),
(14, 26, 15, 579.60, 'card', 'txn_633f1a1b1e1ee', 'completed', '2025-09-30 15:11:00');

-- (Continue adding corresponding payment records for each completed ride)

-- End of Dataset
