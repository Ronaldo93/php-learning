<?php
// Bootstrap file for PHPUnit tests

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define project root
define('PROJECT_ROOT', dirname(__DIR__));

// Set up test database configuration
define('TEST_DB_HOST', 'localhost');
define('TEST_DB_USER', 'user');
define('TEST_DB_PASS', 'Password123!');
define('TEST_DB_NAME', 'rental_test');

// Create test images directory
$test_images_dir = PROJECT_ROOT . '/html/images/test';
if (!is_dir($test_images_dir)) {
    mkdir($test_images_dir, 0755, true);
}

// Helper function to clean up test files
function cleanup_test_files() {
    $test_images_dir = PROJECT_ROOT . '/html/images/test';
    if (is_dir($test_images_dir)) {
        $files = glob($test_images_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

// Helper function to create test database
function setup_test_database() {
    $mysqli = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);

    // Create test database if it doesn't exist
    $mysqli->query("CREATE DATABASE IF NOT EXISTS " . TEST_DB_NAME);
    $mysqli->select_db(TEST_DB_NAME);

    // Create test table
    $mysqli->query("DROP TABLE IF EXISTS rentals");
    $mysqli->query("
        CREATE TABLE rentals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            property_name VARCHAR(255) NOT NULL,
            owner VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL,
            phone VARCHAR(32) NOT NULL,
            image_data LONGBLOB,
            image_url VARCHAR(512)
        )
    ");

    $mysqli->close();
}

// Helper function to clean test database
function cleanup_test_database() {
    $mysqli = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME);
    $mysqli->query("TRUNCATE TABLE rentals");
    $mysqli->close();
}
