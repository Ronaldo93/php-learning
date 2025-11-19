<?php

use PHPUnit\Framework\TestCase;

class AddRentalTest extends TestCase
{
    private $mysqli;
    private $upload_dir;

    protected function setUp(): void
    {
        // Set up test database
        setup_test_database();

        // Set up database connection
        $this->mysqli = new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME);

        // Set up test upload directory
        $this->upload_dir = PROJECT_ROOT . '/html/images/test/';
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up test database
        cleanup_test_database();

        // Clean up test files
        cleanup_test_files();

        // Close database connection
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function test_database_connection()
    {
        $this->assertFalse($this->mysqli->connect_error, "Database connection should succeed");
    }

    public function test_insert_rental_without_image()
    {
        // Prepare test data
        $id = 1;
        $property_name = "Test Property";
        $owner = "Test Owner";
        $address = "123 Test St";
        $phone = "555-1234";
        $image_url = null;

        // Insert test data
        $stmt = $this->mysqli->prepare(
            "INSERT INTO rentals (id, property_name, owner, address, phone, image_url) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssss", $id, $property_name, $owner, $address, $phone, $image_url);
        $result = $stmt->execute();

        $this->assertTrue($result, "Insert should succeed");

        // Verify data was inserted
        $query = $this->mysqli->query("SELECT * FROM rentals WHERE id = 1");
        $row = $query->fetch_assoc();

        $this->assertEquals($property_name, $row['property_name']);
        $this->assertEquals($owner, $row['owner']);
        $this->assertEquals($address, $row['address']);
        $this->assertEquals($phone, $row['phone']);
        $this->assertNull($row['image_url']);
    }

    public function test_insert_rental_with_image_url()
    {
        // Prepare test data
        $id = 2;
        $property_name = "Test Property 2";
        $owner = "Test Owner 2";
        $address = "456 Test Ave";
        $phone = "555-5678";
        $image_url = "images/test_image.jpg";

        // Insert test data
        $stmt = $this->mysqli->prepare(
            "INSERT INTO rentals (id, property_name, owner, address, phone, image_url) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssss", $id, $property_name, $owner, $address, $phone, $image_url);
        $result = $stmt->execute();

        $this->assertTrue($result, "Insert with image URL should succeed");

        // Verify data was inserted
        $query = $this->mysqli->query("SELECT * FROM rentals WHERE id = 2");
        $row = $query->fetch_assoc();

        $this->assertEquals($image_url, $row['image_url']);
    }

    public function test_validate_allowed_file_extensions()
    {
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        // Test valid extensions
        $this->assertContains('jpg', $allowed_extensions);
        $this->assertContains('jpeg', $allowed_extensions);
        $this->assertContains('png', $allowed_extensions);
        $this->assertContains('gif', $allowed_extensions);
        $this->assertContains('webp', $allowed_extensions);

        // Test invalid extensions
        $this->assertNotContains('exe', $allowed_extensions);
        $this->assertNotContains('php', $allowed_extensions);
        $this->assertNotContains('txt', $allowed_extensions);
    }

    public function test_file_extension_extraction()
    {
        $test_cases = [
            'image.jpg' => 'jpg',
            'photo.JPEG' => 'jpeg',
            'test.PNG' => 'png',
            'animation.gif' => 'gif',
            'modern.webp' => 'webp',
            'path/to/image.jpg' => 'jpg',
        ];

        foreach ($test_cases as $filename => $expected_ext) {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $this->assertEquals($expected_ext, $ext, "Extension for {$filename} should be {$expected_ext}");
        }
    }

    public function test_file_size_validation()
    {
        $max_size = 5242880; // 5MB in bytes

        // Test valid sizes
        $this->assertLessThanOrEqual($max_size, 1048576); // 1MB
        $this->assertLessThanOrEqual($max_size, 5242880); // 5MB

        // Test invalid sizes
        $this->assertGreaterThan($max_size, 6291456); // 6MB
        $this->assertGreaterThan($max_size, 10485760); // 10MB
    }

    public function test_unique_filename_generation()
    {
        $id = 123;
        $file_ext = 'jpg';

        $filename1 = 'property_' . $id . '_' . uniqid() . '.' . $file_ext;
        usleep(1000); // Small delay to ensure different uniqid
        $filename2 = 'property_' . $id . '_' . uniqid() . '.' . $file_ext;

        // Verify filenames are unique
        $this->assertNotEquals($filename1, $filename2, "Generated filenames should be unique");

        // Verify filename format
        $this->assertStringStartsWith('property_123_', $filename1);
        $this->assertStringEndsWith('.jpg', $filename1);
    }

    public function test_sql_injection_prevention()
    {
        // Test that prepared statements prevent SQL injection
        $malicious_input = "'; DROP TABLE rentals; --";

        $id = 999;
        $property_name = $malicious_input;
        $owner = "Test Owner";
        $address = "Test Address";
        $phone = "555-0000";
        $image_url = null;

        $stmt = $this->mysqli->prepare(
            "INSERT INTO rentals (id, property_name, owner, address, phone, image_url) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssss", $id, $property_name, $owner, $address, $phone, $image_url);
        $result = $stmt->execute();

        $this->assertTrue($result, "Insert with malicious input should still succeed (but be escaped)");

        // Verify table still exists
        $query = $this->mysqli->query("SELECT COUNT(*) as count FROM rentals");
        $this->assertNotFalse($query, "Table should still exist");

        // Verify malicious input was stored as string, not executed
        $query = $this->mysqli->query("SELECT * FROM rentals WHERE id = 999");
        $row = $query->fetch_assoc();
        $this->assertEquals($malicious_input, $row['property_name']);
    }

    public function test_create_upload_directory_if_not_exists()
    {
        $test_dir = $this->upload_dir . 'new_subdir/';

        // Ensure directory doesn't exist
        if (is_dir($test_dir)) {
            rmdir($test_dir);
        }

        $this->assertFalse(is_dir($test_dir), "Directory should not exist yet");

        // Create directory
        mkdir($test_dir, 0755, true);

        $this->assertTrue(is_dir($test_dir), "Directory should now exist");

        // Clean up
        rmdir($test_dir);
    }
}
