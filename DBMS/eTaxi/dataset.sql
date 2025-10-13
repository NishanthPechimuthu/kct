-- eTaxi Large Demo Dataset
-- Generated on 2025-09-30

SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE Payments;
TRUNCATE TABLE Rides;
TRUNCATE TABLE Subscriptions;
TRUNCATE TABLE Subscription_Plans;
TRUNCATE TABLE Driver_Maintenance;
TRUNCATE TABLE Admins;
TRUNCATE TABLE Drivers;
TRUNCATE TABLE Passengers;
TRUNCATE TABLE Users;

SET FOREIGN_KEY_CHECKS = 1;

-- Hashed password for '2809'
SET @password_hash = '$2y$10$3bYJ1uY.LgCRjUa42nORB.o9c5Ld2mH/y8eA.U.t.Y8eA.U.t.Y8e';

-- 1. Users
-- Core Users
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(1, 'Admin', 'admin@etaxi.ct.ws', @password_hash, '1000000001', 'admin', 1),
(2, 'Main Driver', 'driver@etaxi.ct.ws', @password_hash, '2000000001', 'driver', 1),
(3, 'User One', 'user1@etaxi.ct.ws', @password_hash, '3000000001', 'passenger', 1),
(4, 'User Two', 'user2@etaxi.ct.ws', @password_hash, '4000000001', 'passenger', 1);

-- Additional Drivers (5)
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(5, 'Sunil Kumar', 'sunil.k@example.com', @password_hash, '9876543210', 'driver', 1),
(6, 'Amit Singh', 'amit.s@example.com', @password_hash, '9876543211', 'driver', 1),
(7, 'Rahul Verma', 'rahul.v@example.com', @password_hash, '9876543212', 'driver', 1),
(8, 'Sanjay Das', 'sanjay.d@example.com', @password_hash, '9876543213', 'driver', 1),
(9, 'Mahesh Bhatt', 'mahesh.b@example.com', @password_hash, '9876543214', 'driver', 1);

-- Additional Passengers (10)
INSERT INTO Users (user_id, full_name, email, password_hash, phone_number, role, is_active) VALUES
(10, 'Priya Sharma', 'priya.s@example.com', @password_hash, '8765432101', 'passenger', 1),
(11, 'Anjali Gupta', 'anjali.g@example.com', @password_hash, '8765432102', 'passenger', 1),
(12, 'Kavita Reddy', 'kavita.r@example.com', @password_hash, '8765432103', 'passenger', 1),
(13, 'Ravi Kumar', 'ravi.k@example.com', @password_hash, '8765432104', 'passenger', 1),
(14, 'Arjun Nair', 'arjun.n@example.com', @password_hash, '8765432105', 'passenger', 1),
(15, 'Meera Iyer', 'meera.i@example.com', @password_hash, '8765432106', 'passenger', 1),
(16, 'Vikram Singh', 'vikram.s@example.com', @password_hash, '8765432107', 'passenger', 1),
(17, 'Sneha Patel', 'sneha.p@example.com', @password_hash, '8765432108', 'passenger', 1),
(18, 'Imran Khan', 'imran.k@example.com', @password_hash, '8765432109', 'passenger', 1),
(19, 'Aisha Begum', 'aisha.b@example.com', @password_hash, '8765432110', 'passenger', 1);

-- 2. Role-specific tables
INSERT INTO Admins (admin_id, permissions_level) VALUES (1, 1);
INSERT INTO Drivers (driver_id, license_number, vehicle_details) VALUES 
(2, 'DRV-001', 'Toyota Etios - 2022 - White'),
(5, 'DRV-002', 'Maruti Dzire - 2021 - Silver'),
(6, 'DRV-003', 'Hyundai Xcent - 2023 - Blue'),
(7, 'DRV-004', 'Tata Tigor - 2022 - Red'),
(8, 'DRV-005', 'Honda Amaze - 2021 - Grey'),
(9, 'DRV-006', 'Ford Aspire - 2020 - Black');
INSERT INTO Passengers (passenger_id) VALUES (3), (4), (10), (11), (12), (13), (14), (15), (16), (17), (18), (19);

-- 3. Subscription Plans
INSERT INTO Subscription_Plans (plan_id, plan_name, price, max_rides, max_km_per_ride) VALUES
(1, 'Basic', 2000.00, 100, 50),
(2, 'Standard', 4000.00, 250, 100),
(3, 'Premium', 6000.00, 400, 120);

-- 4. Subscriptions for Core Users
-- User1 gets a Standard plan for September
INSERT INTO Subscriptions (passenger_id, plan_id, start_date, end_date, rides_taken, is_active) VALUES
(3, 2, '2025-09-01', '2025-09-30', 0, 1);
-- User2 gets a Premium plan for the last 3 months
INSERT INTO Subscriptions (passenger_id, plan_id, start_date, end_date, rides_taken, is_active) VALUES
(4, 3, '2025-07-01', '2025-09-30', 0, 1);

-- 5. Rides & Payments (Sample of 25 rides over 3 months)
-- A full dataset would contain ~150 records with more variation.
SET @i = 0;

-- July Rides
INSERT INTO Rides (passenger_id, driver_id, distance_km, fare, ride_status, request_time, start_time, end_time, pickup_location_lat, pickup_location_lng, dropoff_location_lat, dropoff_location_lng) VALUES
(4, 2, 25, 0.00, 'completed', '2025-07-05 10:00:00', '2025-07-05 10:05:00', '2025-07-05 10:40:00', 11.01, 76.95, 11.04, 77.04),
(10, 5, 15, 210.00, 'completed', '2025-07-08 12:00:00', '2025-07-08 12:05:00', '2025-07-08 12:35:00', 11.02, 76.96, 10.9, 76.8),
(4, 6, 80, 0.00, 'completed', '2025-07-15 18:30:00', '2025-07-15 18:35:00', '2025-07-15 19:45:00', 10.94, 76.82, 11.07, 76.98),
(11, 7, 30, 390.00, 'completed', '2025-07-22 09:00:00', '2025-07-22 09:05:00', '2025-07-22 09:55:00', 10.65, 77.01, 11.02, 76.96),
(4, 8, 110, 0.00, 'completed', '2025-07-28 21:00:00', '2025-07-28 21:05:00', '2025-07-28 22:35:00', 11.02, 76.94, 10.67, 77.00);

-- August Rides
INSERT INTO Rides (passenger_id, driver_id, distance_km, fare, ride_status, request_time, start_time, end_time, pickup_location_lat, pickup_location_lng, dropoff_location_lat, dropoff_location_lng) VALUES
(12, 9, 5, 90.00, 'completed', '2025-08-02 11:00:00', '2025-08-02 11:05:00', '2025-08-02 11:20:00', 10.94, 76.91, 11.01, 76.95),
(4, 2, 45, 0.00, 'completed', '2025-08-10 14:20:00', '2025-08-10 14:25:00', '2025-08-10 15:10:00', 11.00, 76.96, 11.02, 76.93),
(13, 5, 22, 294.00, 'completed', '2025-08-15 16:00:00', '2025-08-15 16:05:00', '2025-08-15 16:45:00', 10.58, 77.00, 10.65, 77.01),
(4, 6, 95, 0.00, 'completed', '2025-08-25 07:00:00', '2025-08-25 07:05:00', '2025-08-25 08:30:00', 11.01, 76.95, 10.9, 76.7),
(14, 7, 18, 246.00, 'completed', '2025-08-29 19:00:00', '2025-08-29 19:05:00', '2025-08-29 19:40:00', 11.03, 77.04, 11.02, 76.96);

-- September Rides
INSERT INTO Rides (passenger_id, driver_id, distance_km, fare, ride_status, request_time, start_time, end_time, pickup_location_lat, pickup_location_lng, dropoff_location_lat, dropoff_location_lng) VALUES
(3, 8, 10, 0.00, 'completed', '2025-09-02 09:30:00', '2025-09-02 09:35:00', '2025-09-02 09:55:00', 10.94, 76.82, 11.02, 76.96),
(15, 9, 12, 174.00, 'completed', '2025-09-05 13:00:00', '2025-09-05 13:05:00', '2025-09-05 13:35:00', 11.07, 76.98, 10.65, 77.01),
(3, 2, 55, 0.00, 'completed', '2025-09-10 15:00:00', '2025-09-10 15:05:00', '2025-09-10 16:00:00', 10.66, 77.00, 11.02, 76.96),
(16, 5, 8, 126.00, 'completed', '2025-09-15 18:00:00', '2025-09-15 18:05:00', '2025-09-15 18:25:00', 11.02, 76.94, 10.94, 76.91),
(4, 6, 115, 0.00, 'completed', '2025-09-20 22:00:00', '2025-09-20 22:05:00', '2025-09-20 23:45:00', 11.01, 76.95, 10.9, 76.68),
(3, 7, 90, 0.00, 'completed', '2025-09-25 11:00:00', '2025-09-25 11:05:00', '2025-09-25 12:30:00', 11.03, 77.04, 10.65, 77.01),
(17, 8, 28, 366.00, 'completed', '2025-09-28 10:00:00', '2025-09-28 10:05:00', '2025-09-28 10:50:00', 10.58, 77.00, 11.02, 76.93),
(18, 9, 3, 66.00, 'cancelled_by_passenger', '2025-09-29 12:00:00', NULL, NULL, 11.01, 76.95, 11.02, 76.96),
(3, 2, 70, 0.00, 'completed', '2025-09-30 16:00:00', '2025-09-30 16:05:00', '2025-09-30 17:15:00', 10.94, 76.91, 10.67, 77.00),
(19, 5, 19, 258.00, 'completed', '2025-09-30 20:00:00', '2025-09-30 20:05:00', '2025-09-30 20:45:00', 11.02, 76.96, 11.07, 76.98);

-- Update subscription counts based on the rides above
UPDATE Subscriptions SET rides_taken = 5 WHERE passenger_id = 4;
UPDATE Subscriptions SET rides_taken = 4 WHERE passenger_id = 3;

-- Create payments for all completed rides
INSERT INTO Payments (user_id, ride_id, amount, payment_method, transaction_id, payment_status, payment_date)
SELECT passenger_id, ride_id, fare, 'card', CONCAT('txn-', ride_id), 'completed', end_time 
FROM Rides WHERE ride_status = 'completed';

-- End of Dataset
