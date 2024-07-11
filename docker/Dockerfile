# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Prevents prompts during installation
ENV DEBIAN_FRONTEND=noninteractive

# Update package list and install necessary packages
RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        zlib1g-dev \
        wget \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        libssl-dev \
        libmcrypt-dev \
        libxslt-dev \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql zip gd mbstring xml curl xsl

# Set the working directory
WORKDIR /var/www/html

# Copy your application code to the container's filesystem
COPY src/ /var/www/html/

# Expose port 80 to allow outside access to your container
EXPOSE 80

# Define the command to run your application
CMD ["apache2-foreground"]
