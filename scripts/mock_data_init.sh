#!/bin/bash
# check if there is any exist data in table (assuming no password on mysql)
count_rental=$(sudo mysql -u root -N -B -e "source sql/03-count_rental_data.sql;")

# if greater than 0 -> print already exist
if [[ -n "$count_rental" && $count_rental -gt 0 ]]; then
	echo "Already exist"
elif [[ -n "$count_rental" && $count_rental -eq 0 ]]; then
	# otherwise, source sql and create data
	# load data - enable local inline to load.
	sudo mysql --local-infile=1 -u root -e "source sql/02-load_sample_data.sql;"
fi
