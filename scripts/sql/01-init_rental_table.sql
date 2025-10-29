-- select the rental database first
USE rental;

-- schema for rentals table
CREATE TABLE rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_name VARCHAR(255) NOT NULL,
  owner VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(32) NOT NULL,
  image_data LONGBLOB,
  image_url VARCHAR(512)
);


