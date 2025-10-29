#!/bin/bash
# we assume you only install mysql on your local machine AND haven't used mysql_secure_installation yet (recommended)
# with that, you can just no type in the password for the root user and speed up the setup (aside from sudo password)
# run this as root

# check if the database registered
is_registered=$(mysql -u root -p -e "SHOW DATABASES LIKE 'rental';")

# create the database if not registered
if [[ "$is_registered" == "" ]]; then
  # create database
  mysql -u root -p -e "source sql/00-setup_rental_database.sql;"
  echo "Database haven't created . We have created it for you."
fi

# create table if not registered
is_table_registered=$(mysql -u root -p -e "SHOW TABLES FROM rental;")
if [[ "$is_table_registered" == "" ]]; then
  # use the file from the scripts/sql folder
  mysql -u root -p -e "SOURCE sql/01-init_rental_table.sql;"
fi

echo "All set up is done."
