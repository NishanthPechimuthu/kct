-- Database Schema for eTaxi Management System

-- Main table for all users, handling authentication and basic info
CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) UNIQUE,
    role ENUM('passenger', 'driver', 'admin') NOT NULL,
    verification_token VARCHAR(64) NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT true
);

-- Passenger-specific details
CREATE TABLE Passengers (
    passenger_id INT PRIMARY KEY,
    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (passenger_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Driver-specific details
CREATE TABLE Drivers (
    driver_id INT PRIMARY KEY,
    license_number VARCHAR(50) NOT NULL UNIQUE,
    vehicle_details VARCHAR(255) NOT NULL COMMENT 'e.g., "Toyota Prius - 2022 - White"',
    current_status ENUM('available', 'on_trip', 'offline', 'maintenance_due') DEFAULT 'offline',
    rating DECIMAL(3, 2) DEFAULT 5.00,
    FOREIGN KEY (driver_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Admin-specific details (can be extended if needed)
CREATE TABLE Admins (
    admin_id INT PRIMARY KEY,
    permissions_level INT DEFAULT 1,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Plans that admins can manage (CRUD)
CREATE TABLE Subscription_Plans (
    plan_id INT PRIMARY KEY AUTO_INCREMENT,
    plan_name VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    max_rides INT NOT NULL,
    max_km_per_ride INT NOT NULL,
    extra_km_charge_percent DECIMAL(5, 2) DEFAULT 10.00 COMMENT 'Percentage of base fare per extra km',
    extra_ride_charge_percent DECIMAL(5, 2) DEFAULT 25.00 COMMENT 'Percentage of base fare for rides beyond limit',
    description TEXT,
    is_available BOOLEAN DEFAULT true
);

-- Subscription tracking for passengers, linked to a specific plan
CREATE TABLE Subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    rides_taken INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    FOREIGN KEY (passenger_id) REFERENCES Passengers(passenger_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES Subscription_Plans(plan_id)
);

-- Core table for all rides
CREATE TABLE Rides (
    ride_id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_id INT NOT NULL,
    driver_id INT,
    pickup_location_lat DECIMAL(10, 8) NOT NULL,
    pickup_location_lng DECIMAL(11, 8) NOT NULL,
    dropoff_location_lat DECIMAL(10, 8) NOT NULL,
    dropoff_location_lng DECIMAL(11, 8) NOT NULL,
    pickup_address VARCHAR(255),
    dropoff_address VARCHAR(255),
    distance_km DECIMAL(10, 2) NOT NULL,
    fare DECIMAL(10, 2) NOT NULL,
    otp VARCHAR(6) NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending' NOT NULL,
    ride_status ENUM('requested', 'accepted', 'in_progress', 'completed', 'cancelled_by_passenger', 'cancelled_by_driver', 'disputed') NOT NULL,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    FOREIGN KEY (passenger_id) REFERENCES Passengers(passenger_id),
    FOREIGN KEY (driver_id) REFERENCES Drivers(driver_id)
);

-- Payment records for both subscriptions and per-ride payments
CREATE TABLE Payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ride_id INT NULL,
    subscription_id INT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'card',
    transaction_id VARCHAR(255) NOT NULL UNIQUE,
    transaction_details JSON, -- Add this line
    payment_status ENUM('pending', 'completed', 'failed') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (ride_id) REFERENCES Rides(ride_id),
    FOREIGN KEY (subscription_id) REFERENCES Subscriptions(subscription_id)
);

-- Driver maintenance and document tracking
CREATE TABLE Driver_Maintenance (
    maintenance_id INT PRIMARY KEY AUTO_INCREMENT,
    driver_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL COMMENT 'e.g., "Vehicle Service Record", "Insurance"',
    document_path VARCHAR(255) NOT NULL,
    upload_date DATE NOT NULL,
    next_due_date DATE NOT NULL,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_id INT NULL COMMENT 'Admin who reviewed the document',
    review_date TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (driver_id) REFERENCES Drivers(driver_id),
    FOREIGN KEY (admin_id) REFERENCES Admins(admin_id)
);
