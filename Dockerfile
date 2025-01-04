# Use PHP with Apache as base image
FROM php:8.2-apache

# Create the developers group
RUN groupadd developers

# Create user "me" and add to developers group
RUN useradd -m -g developers me

# Install PHP extensions and required packages
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    sqlite3 libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && docker-php-ext-install pdo_sqlite

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY ./src/ /var/www/html/

# Set permissions
RUN chown me:developers -R /var/www/html