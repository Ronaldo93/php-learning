#!/bin/bash

# Simple manual test script for testing add_rental.php endpoint
# This script allows you to quickly test the upload functionality

# Color codes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}Manual Test for add_rental.php${NC}\n"

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Create test image if it doesn't exist
TEST_IMAGE="$PROJECT_ROOT/tests/fixtures/test_image.jpg"
if [ ! -f "$TEST_IMAGE" ]; then
    echo -e "${YELLOW}Creating test image...${NC}"
    php "$PROJECT_ROOT/tests/fixtures/create_test_image.php"
fi

# Test data
ID=$((RANDOM % 9000 + 1000))
PROPERTY_NAME="Manual Test Property $ID"
OWNER="Test Owner"
ADDRESS="123 Test Street, Test City"
PHONE="555-1234"

echo -e "${YELLOW}Test Data:${NC}"
echo "  ID: $ID"
echo "  Property Name: $PROPERTY_NAME"
echo "  Owner: $OWNER"
echo "  Address: $ADDRESS"
echo "  Phone: $PHONE"
echo "  Image: $TEST_IMAGE"
echo ""

# Check if web server is running
echo -e "${YELLOW}Checking web server...${NC}"
if ! curl -s -o /dev/null -w "%{http_code}" http://localhost/rental.php | grep -q "200\|302"; then
    echo -e "${RED}✗ Web server not running at http://localhost/${NC}"
    echo "  Please start your web server first"
    exit 1
fi
echo -e "${GREEN}✓ Web server is running${NC}\n"

# Send test request
echo -e "${YELLOW}Sending test request...${NC}"
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST \
    -F "id=$ID" \
    -F "property_name=$PROPERTY_NAME" \
    -F "owner=$OWNER" \
    -F "address=$ADDRESS" \
    -F "phone=$PHONE" \
    -F "property_image=@$TEST_IMAGE" \
    http://localhost/add_rental.php)

HTTP_CODE=$(echo "$RESPONSE" | tail -n 1)

if [ "$HTTP_CODE" = "302" ] || echo "$RESPONSE" | grep -q "Location: rental.php"; then
    echo -e "${GREEN}✓ Test passed! Property added successfully${NC}"
    echo -e "${GREEN}  HTTP Code: $HTTP_CODE${NC}"
    echo ""
    echo -e "${BLUE}View the results at: http://localhost/rental.php${NC}"
else
    echo -e "${RED}✗ Test failed${NC}"
    echo -e "${RED}  HTTP Code: $HTTP_CODE${NC}"
    echo ""
    echo "Response:"
    echo "$RESPONSE" | head -n -1
fi
