-- Database creation
CREATE DATABASE IF NOT EXISTS cabshare;
USE cabshare;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('driver', 'passenger') NOT NULL,
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rides table
CREATE TABLE rides (
    id INT PRIMARY KEY AUTO_INCREMENT,
    driver_id INT NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    ride_date DATE NOT NULL,
    ride_time TIME NOT NULL,
    available_seats INT NOT NULL,
    fare_per_seat DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id)
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ride_id INT NOT NULL,
    passenger_id INT NOT NULL,
    num_seats INT NOT NULL,
    total_fare DECIMAL(10,2) NOT NULL,
    payment_id VARCHAR(255),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    booking_status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ride_id) REFERENCES rides(id),
    FOREIGN KEY (passenger_id) REFERENCES users(id)
); 


CREATE TABLE driver_details (
    user_id INT PRIMARY KEY, -- Foreign key referencing the users table
    vehicle_number VARCHAR(50) NOT NULL,
    vehicle_model VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    driving_experience INT NOT NULL DEFAULT 0,
    vehicle_type ENUM('sedan', 'suv', 'hatchback') NOT NULL DEFAULT 'sedan',
    insurance_number VARCHAR(50) NOT NULL DEFAULT '',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE users 

ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other') NULL AFTER phone_number,
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL AFTER gender;

CREATE TABLE ride_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_id INT NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    dropoff_location VARCHAR(255) NOT NULL,
    ride_date DATE NOT NULL,
    ride_time TIME NOT NULL,
    num_seats INT NOT NULL,
    max_price_per_seat DECIMAL(10,2),
    min_driver_rating DECIMAL(3,2),
    status ENUM('pending', 'accepted', 'cancelled', 'expired') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (passenger_id) REFERENCES users(id)
);

-- Add a column to rides table to link it with a request
ALTER TABLE rides ADD COLUMN request_id INT NULL;
ALTER TABLE rides ADD FOREIGN KEY (request_id) REFERENCES ride_requests(id); 
