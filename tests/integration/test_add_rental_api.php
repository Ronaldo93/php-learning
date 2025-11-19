<?php
/**
 * Integration test for add_rental.php
 * This script simulates form submission with file upload
 */

// Configuration
$base_url = "http://localhost/";
$endpoint = $base_url . "add_rental.php";

// Test data
$test_data = [
    [
        'name' => 'Test without image',
        'data' => [
            'id' => 1000,
            'property_name' => 'Test Property No Image',
            'owner' => 'Test Owner',
            'address' => '123 Test Street',
            'phone' => '555-1234'
        ],
        'image' => null
    ],
    [
        'name' => 'Test with image',
        'data' => [
            'id' => 1001,
            'property_name' => 'Test Property With Image',
            'owner' => 'Test Owner 2',
            'address' => '456 Test Avenue',
            'phone' => '555-5678'
        ],
        'image' => __DIR__ . '/../fixtures/test_image.jpg'
    ]
];

// Color output helpers
function print_success($message) {
    echo "\033[32m✓ {$message}\033[0m\n";
}

function print_error($message) {
    echo "\033[31m✗ {$message}\033[0m\n";
}

function print_info($message) {
    echo "\033[34mℹ {$message}\033[0m\n";
}

// Test function
function test_add_rental($endpoint, $test_name, $data, $image_path = null) {
    print_info("Running test: {$test_name}");

    // Prepare POST fields
    $post_fields = $data;

    // Prepare file upload if provided
    if ($image_path && file_exists($image_path)) {
        $post_fields['property_image'] = new CURLFile($image_path);
    }

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);

    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($curl_error) {
        print_error("cURL Error: {$curl_error}");
        return false;
    }

    // Parse response
    list($headers, $body) = explode("\r\n\r\n", $response, 2);

    // Check for redirect (success)
    if ($http_code == 302 || strpos($headers, 'Location: rental.php') !== false) {
        print_success("Test passed: {$test_name}");
        print_info("HTTP Code: {$http_code}");
        return true;
    } else {
        print_error("Test failed: {$test_name}");
        print_error("HTTP Code: {$http_code}");
        if ($body) {
            print_error("Response: " . substr($body, 0, 200));
        }
        return false;
    }
}

// Run tests
echo "\n";
echo "===========================================\n";
echo "Integration Tests for add_rental.php\n";
echo "===========================================\n\n";

$passed = 0;
$failed = 0;

foreach ($test_data as $test) {
    $result = test_add_rental(
        $endpoint,
        $test['name'],
        $test['data'],
        $test['image']
    );

    if ($result) {
        $passed++;
    } else {
        $failed++;
    }

    echo "\n";
}

// Summary
echo "===========================================\n";
echo "Test Summary\n";
echo "===========================================\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";
echo "Total:  " . ($passed + $failed) . "\n";
echo "===========================================\n\n";

exit($failed > 0 ? 1 : 0);
