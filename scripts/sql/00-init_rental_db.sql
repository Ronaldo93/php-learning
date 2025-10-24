CREATE TABLE rentals (
  "id" INT AUTO_INCREMENT PRIMARY KEY ,
  "property_name" TEXT NOT NULL,
  "owner" TEXT NOT NULL,
  "address" TEXT NOT NULL,
  "phone" TEXT NOT NULL,
  "image_data" BLOB NOT NULL,
)
