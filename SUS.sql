CREATE DATABASE IF NOT EXISTS SUS;
USE SUS;


CREATE TABLE IF NOT EXISTS user_data (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Email_ID VARCHAR(255) NOT NULL UNIQUE,
    Username VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    Password VARCHAR(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS tire_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_name VARCHAR(100),
    front_left_pressure DECIMAL(4,2),
    front_right_pressure DECIMAL(4,2),
    rear_left_pressure DECIMAL(4,2),
    rear_right_pressure DECIMAL(4,2),
    front_left_temp DECIMAL(4,2),
    front_right_temp DECIMAL(4,2),
    rear_left_temp DECIMAL(4,2),
    rear_right_temp DECIMAL(4,2),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_data(ID)
);

CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    type ENUM('car', 'suv', 'truck', 'motorcycle') DEFAULT 'car',
    vin VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user_data(ID)
);
