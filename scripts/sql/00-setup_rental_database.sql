CREATE DATABASE rental;

-- create the user (if not already exists)
CREATE USER 'user'@'localhost' IDENTIFIED BY 'Password123!';

-- grant privileges
GRANT ALL PRIVILEGES ON rental.* TO 'user'@'localhost';

-- apply changes
FLUSH PRIVILEGES;
