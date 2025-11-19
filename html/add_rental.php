<?php
// Database connection
$mysqli = new mysqli("localhost", "user", "Password123!", "rental");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get form data
    $id = $mysqli->real_escape_string($_POST['id']);
    $property_name = $mysqli->real_escape_string($_POST['property_name']);
    $owner = $mysqli->real_escape_string($_POST['owner']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $phone = $mysqli->real_escape_string($_POST['phone']);

    $image_url = null;

    // Handle file upload
    if (isset($_FILES['property_image']) && $_FILES['property_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/images/';

        // Create images directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Get file information
        $file_tmp = $_FILES['property_image']['tmp_name'];
        $file_name = $_FILES['property_image']['name'];
        $file_size = $_FILES['property_image']['size'];
        $file_error = $_FILES['property_image']['error'];

        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        // Validate file extension
        if (in_array($file_ext, $allowed_extensions)) {
            // Check file size (max 5MB)
            if ($file_size <= 5242880) {
                // Generate unique filename to prevent overwriting
                $new_filename = 'property_' . $id . '_' . uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;

                // Move uploaded file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Store relative path for database
                    $image_url = 'images/' . $new_filename;
                } else {
                    die("Error: Failed to upload file.");
                }
            } else {
                die("Error: File size exceeds 5MB limit.");
            }
        } else {
            die("Error: Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP are allowed.");
        }
    }

    // Insert into database
    $stmt = $mysqli->prepare("INSERT INTO rentals (id, property_name, owner, address, phone, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id, $property_name, $owner, $address, $phone, $image_url);

    if ($stmt->execute()) {
        // Success - redirect back to rental page
        header("Location: rental.php?success=1");
        exit();
    } else {
        // Error
        die("Error: " . $stmt->error);
    }

    $stmt->close();
}

$mysqli->close();
?>
