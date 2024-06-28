#!/bin/bash

# Update the package repository
sudo yum update -y

# Install Apache web server
sudo yum install -y httpd

# Start Apache web server
sudo systemctl start httpd
sudo systemctl enable httpd

# Install PHP
sudo yum install -y php

# Restart Apache to load PHP module
sudo systemctl restart httpd

# Navigate to the application directory
cd /var/www/html

# Install any PHP dependencies (if using Composer, uncomment the next lines)
# curl -sS https://getcomposer.org/installer | php
# php composer.phar install

# Ensure correct permissions for the web server
sudo chown -R apache:apache /var/www/html
sudo chmod -R 755 /var/www/html

# Optional: Install any additional software or dependencies your application needs
# sudo yum install -y <additional-dependencies>

echo "Deployment script completed successfully."

