<<<<<<< HEAD

sudo apt-get update
sudo apt-get install -y apache2
=======
#!/bin/bash

# Update the package repository
sudo apt-get update

# Install Apache web server
sudo apt-get install -y apache2

# Start Apache web server
sudo systemctl start apache2
sudo systemctl enable apache2

# Install PHP and Apache PHP module
sudo apt-get install -y php libapache2-mod-php

# Restart Apache to load PHP module
sudo systemctl restart apache2

# Navigate to the application directory
cd /var/www/html

# Ensure correct permissions for the web server
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# Optional: Install any additional software or dependencies your application needs
# sudo apt-get install -y <additional-dependencies>

echo "Deployment script completed successfully."
>>>>>>> ce5a25e94f94fb4767bdeccd8940613c841f5f1a
