# Test Suite for Rental Application

This directory contains comprehensive tests for the rental application, specifically for the `add_rental.php` functionality.

## Test Structure

```
tests/
├── bootstrap.php                 # Test bootstrap and helper functions
├── unit/                        # Unit tests
│   └── AddRentalTest.php       # Unit tests for add_rental functionality
├── integration/                 # Integration tests
│   └── test_add_rental_api.php # API integration tests
└── fixtures/                    # Test fixtures
    ├── create_test_image.php   # Script to generate test image
    └── test_image.jpg          # Generated test image
```

## Prerequisites

### For Unit Tests
- PHP 7.4 or higher
- PHPUnit 9.5 or higher
- MySQL database with test credentials

Install PHPUnit:
```bash
composer require --dev phpunit/phpunit
```

### For Integration Tests
- Running web server (Apache/Nginx)
- PHP cURL extension enabled
- Application accessible at http://localhost/

## Running Tests

### Option 1: Run All Tests (Recommended)
```bash
./scripts/run_tests.bash
```

This script will:
1. Create test fixtures
2. Set up test database
3. Run unit tests
4. Run integration tests
5. Display summary

### Option 2: Run Unit Tests Only
```bash
phpunit --testsuite Unit
```

### Option 3: Run Integration Tests Only
```bash
php tests/integration/test_add_rental_api.php
```

### Option 4: Manual Quick Test
```bash
./scripts/manual_test.bash
```

This performs a single test with random data to verify the endpoint works.

## Test Coverage

### Unit Tests (AddRentalTest.php)

1. **test_database_connection**
   - Verifies database connection works

2. **test_insert_rental_without_image**
   - Tests inserting property without image
   - Validates all fields are stored correctly

3. **test_insert_rental_with_image_url**
   - Tests inserting property with image URL
   - Verifies image URL is stored

4. **test_validate_allowed_file_extensions**
   - Tests file extension validation
   - Ensures only safe file types are allowed

5. **test_file_extension_extraction**
   - Tests filename parsing
   - Validates extension extraction logic

6. **test_file_size_validation**
   - Tests file size limits (5MB max)
   - Ensures oversized files are rejected

7. **test_unique_filename_generation**
   - Tests filename uniqueness
   - Verifies no filename collisions

8. **test_sql_injection_prevention**
   - Tests SQL injection prevention
   - Validates prepared statements work correctly

9. **test_create_upload_directory_if_not_exists**
   - Tests automatic directory creation
   - Validates upload directory setup

### Integration Tests (test_add_rental_api.php)

1. **Test without image**
   - Submits form without image upload
   - Verifies successful redirect

2. **Test with image**
   - Submits form with image file
   - Verifies file upload and redirect

## Test Database

Tests use a separate database: `rental_test`

Schema:
```sql
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_name VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    image_data LONGBLOB,
    image_url VARCHAR(512)
);
```

## Creating Test Fixtures

Generate test image:
```bash
php tests/fixtures/create_test_image.php
```

This creates a 400x300 JPG image for testing uploads.

## Cleanup

Test cleanup is automatic:
- Unit tests clean up after each test
- Test files are removed from `html/images/test/`
- Test database is truncated between tests

## Troubleshooting

### PHPUnit not found
```bash
composer require --dev phpunit/phpunit
```

### Web server not running
Start your web server:
```bash
# For built-in PHP server:
php -S localhost:80 -t html/

# Or configure Apache/Nginx
```

### Database connection failed
Check credentials in `tests/bootstrap.php`:
- Host: localhost
- User: user
- Password: Password123!
- Database: rental_test

### Permission denied for uploads
Run:
```bash
./scripts/setup_upload_permissions.bash
```

## CI/CD Integration

Add to your CI pipeline:
```yaml
test:
  script:
    - ./scripts/run_tests.bash
```

## Security Tests Included

- SQL injection prevention
- File type validation
- File size limits
- Input sanitization
- Path traversal prevention
