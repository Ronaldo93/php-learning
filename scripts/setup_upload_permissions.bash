#!/bin/bash

# Script to set proper permissions for image uploads
# This script ensures the web server can write to the images directory

echo "Setting up upload directory permissions..."

# Get the directory where this script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
IMAGES_DIR="$PROJECT_ROOT/html/images"

# Create images directory if it doesn't exist
if [ ! -d "$IMAGES_DIR" ]; then
    echo "Creating images directory..."
    mkdir -p "$IMAGES_DIR"
fi

# Set permissions for images directory
# 755 allows owner full access, and read/execute for group and others
chmod 755 "$IMAGES_DIR"

echo "Permissions set successfully!"
echo "Images directory: $IMAGES_DIR"
echo "Permissions: $(stat -c %a "$IMAGES_DIR" 2>/dev/null || stat -f %A "$IMAGES_DIR" 2>/dev/null)"

# If running on Linux with Apache, you might need to change ownership
# Uncomment the following lines if needed:
# echo "Note: If using Apache, you may need to run:"
# echo "  sudo chown -R www-data:www-data $IMAGES_DIR"
# echo "Or for Nginx:"
# echo "  sudo chown -R nginx:nginx $IMAGES_DIR"

echo "Setup complete!"
