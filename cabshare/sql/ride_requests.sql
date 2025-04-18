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