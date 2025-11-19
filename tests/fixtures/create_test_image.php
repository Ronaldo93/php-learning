<?php
/**
 * Script to generate a test image for testing file uploads
 */

$width = 400;
$height = 300;

// Create image
$image = imagecreatetruecolor($width, $height);

// Allocate colors
$bg_color = imagecolorallocate($image, 52, 152, 219); // Blue background
$text_color = imagecolorallocate($image, 255, 255, 255); // White text
$border_color = imagecolorallocate($image, 41, 128, 185); // Darker blue border

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Draw border
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border_color);
imagerectangle($image, 1, 1, $width - 2, $height - 2, $border_color);

// Add text
$text = "TEST IMAGE";
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;
imagestring($image, $font_size, $x, $y, $text, $text_color);

// Add dimensions text
$dims_text = "{$width}x{$height}";
$dims_x = ($width - imagefontwidth(3) * strlen($dims_text)) / 2;
imagestring($image, 3, $dims_x, $y + 40, $dims_text, $text_color);

// Save image
$output_path = __DIR__ . '/test_image.jpg';
imagejpeg($image, $output_path, 90);

// Clean up
imagedestroy($image);

echo "Test image created: {$output_path}\n";
echo "Size: " . filesize($output_path) . " bytes\n";
