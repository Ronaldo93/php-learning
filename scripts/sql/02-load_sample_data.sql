USE rental;

-- load csv file into rentals table
LOAD DATA LOCAL INFILE './sample_data.csv'
INTO TABLE rentals

-- model how the csv is structured
FIELDS TERMINATED BY ',' ENCLOSED BY '"' ESCAPED BY '\\'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(property_name, owner, address, phone, image_data, image_url);

