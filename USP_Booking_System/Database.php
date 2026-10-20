CREATE DATABASE IF NOT EXISTS usp_equipment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE usp_equipment_db;

-- Create Users Table (Handles both Students and Staff/Admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Equipment Table (Inventory management)
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    qty INT NOT NULL DEFAULT 0,
    image_url TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Time Slots Table (For scheduling hourly bookings)
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_date DATE NOT NULL,
    formatted_time VARCHAR(50) NOT NULL,
    is_blocked TINYINT(1) DEFAULT 0,
    is_booked TINYINT(1) DEFAULT 0
);

-- Create Bookings Table (Tracks reservations, statuses, and penalties)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    use_date DATE NOT NULL,
    use_time VARCHAR(50),
    duration_hours INT DEFAULT 2,
    status VARCHAR(20) DEFAULT 'Pending',
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    fine_status VARCHAR(20) DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- Create Settings Table (Manages weekend booking rules, future date limits, etc.)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    allow_weekends TINYINT(1) DEFAULT 0,
    start_time TIME DEFAULT '08:00:00',
    end_time TIME DEFAULT '20:00:00',
    future_weeks_allowed INT DEFAULT 1
);

-- Insert Sample Admin and Student Accounts (Passwords should be hashed via password_hash() in PHP)
INSERT INTO users (username, password, role, full_name) VALUES 
('admin01', '$2y$10$YourHashedPasswordHere', 'admin', 'josua.nata'),
('S11186372', '$2y$10$YourHashedPasswordHere', 'student', 'Josua Nata');

-- Insert Initial Equipment Inventory
INSERT INTO equipment (name, category, qty, description) VALUES 
('Dell Latitude 5420', 'Laptop', 5, 'Core i5, 16GB RAM, 256GB SSD for student projects.'),
('Canon EOS Rebel T7', 'Camera', 3, 'DSLR camera package with standard 18-55mm lens.'),
('Epson PowerLite Projector', 'Projector', 2, 'Portable multimedia classroom projector with HDMI cables.'),
('Logitech Wireless Mouse', 'Mouse', 10, 'Optical wireless mouse accessory.'),
('JBL Portable Speaker', 'Speakers', 4, 'Bluetooth audio speaker for presentations.');

-- Insert Default System Settings
INSERT INTO settings (id, allow_weekends, start_time, end_time, future_weeks_allowed) 
VALUES (1, 0, '08:00:00', '20:00:00', 1)
ON DUPLICATE KEY UPDATE id=id;