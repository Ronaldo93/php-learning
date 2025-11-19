#!/bin/bash

# Script to run all tests for the rental application

set -e  # Exit on error

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo -e "${BLUE}======================================${NC}"
echo -e "${BLUE}Running Tests for Rental Application${NC}"
echo -e "${BLUE}======================================${NC}\n"

# Step 1: Create test fixtures
echo -e "${YELLOW}[1/4] Creating test fixtures...${NC}"
cd "$PROJECT_ROOT"
php tests/fixtures/create_test_image.php
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Test fixtures created${NC}\n"
else
    echo -e "${RED}✗ Failed to create test fixtures${NC}\n"
    exit 1
fi

# Step 2: Set up test database
echo -e "${YELLOW}[2/4] Setting up test database...${NC}"
mysql -u user -pPassword123! << EOF
DROP DATABASE IF EXISTS rental_test;
CREATE DATABASE rental_test;
USE rental_test;
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_name VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    image_data LONGBLOB,
    image_url VARCHAR(512)
);
EOF
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Test database created${NC}\n"
else
    echo -e "${RED}✗ Failed to set up test database${NC}\n"
    exit 1
fi

# Step 3: Run unit tests
echo -e "${YELLOW}[3/4] Running unit tests...${NC}"
if command -v phpunit &> /dev/null; then
    phpunit --testsuite Unit
    UNIT_TEST_RESULT=$?
    if [ $UNIT_TEST_RESULT -eq 0 ]; then
        echo -e "${GREEN}✓ Unit tests passed${NC}\n"
    else
        echo -e "${RED}✗ Unit tests failed${NC}\n"
    fi
else
    echo -e "${YELLOW}⚠ PHPUnit not found. Skipping unit tests.${NC}"
    echo -e "${YELLOW}  Install PHPUnit: composer require --dev phpunit/phpunit${NC}\n"
    UNIT_TEST_RESULT=0
fi

# Step 4: Run integration tests (if web server is running)
echo -e "${YELLOW}[4/4] Running integration tests...${NC}"
if curl -s -o /dev/null -w "%{http_code}" http://localhost/rental.php | grep -q "200\|302"; then
    php tests/integration/test_add_rental_api.php
    INTEGRATION_TEST_RESULT=$?
    if [ $INTEGRATION_TEST_RESULT -eq 0 ]; then
        echo -e "${GREEN}✓ Integration tests passed${NC}\n"
    else
        echo -e "${RED}✗ Integration tests failed${NC}\n"
    fi
else
    echo -e "${YELLOW}⚠ Web server not running at http://localhost/${NC}"
    echo -e "${YELLOW}  Start your web server to run integration tests${NC}\n"
    INTEGRATION_TEST_RESULT=0
fi

# Summary
echo -e "${BLUE}======================================${NC}"
echo -e "${BLUE}Test Summary${NC}"
echo -e "${BLUE}======================================${NC}"

if [ $UNIT_TEST_RESULT -eq 0 ] && [ $INTEGRATION_TEST_RESULT -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed.${NC}"
    exit 1
fi
