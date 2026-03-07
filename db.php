<?php
// Database configuration
// Using environment variable for Docker compatibility, defaulting to localhost for standard XAMPP
$servername = getenv('DB_HOST') ?: "localhost";
$username = "root";
$password = "";
$dbname = "travelo";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Select the database
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Create tables
$table_flight = "CREATE TABLE IF NOT EXISTS flights (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_type VARCHAR(30),
    from_city VARCHAR(50),
    to_city VARCHAR(50),
    depart_date VARCHAR(30),
    return_date VARCHAR(30),
    adults INT,
    children INT,
    infants INT,
    travel_class VARCHAR(30),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($table_flight);

$table_hotel = "CREATE TABLE IF NOT EXISTS hotels (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    check_in VARCHAR(30),
    hotel_search VARCHAR(100),
    accommodations VARCHAR(50),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($table_hotel);

$table_cab = "CREATE TABLE IF NOT EXISTS cabs (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_type VARCHAR(30),
    pickup_type VARCHAR(50),
    from_city VARCHAR(100),
    to_city VARCHAR(100),
    pickup_date VARCHAR(30),
    pickup_time VARCHAR(20),
    return_date VARCHAR(30),
    return_time VARCHAR(20),
    hours VARCHAR(20),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($table_cab);

$table_contact = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(50),
    phone VARCHAR(20),
    website VARCHAR(100),
    message TEXT,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($table_contact);

$table_admin = "CREATE TABLE IF NOT EXISTS admins (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_admin);

// Seed admin data (username: admin, password: password123)
$admin_check = $conn->query("SELECT * FROM admins WHERE username='admin'");
if ($admin_check->num_rows == 0) {
    $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admins (username, password) VALUES ('admin', '$hashed_password')");
}

?>
