FROM php:8.2-apache

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key and run migrations
CMD bash -c 'php artisan key:generate --force && php artisan migrate:refresh --seed --force && apache2-foreground'
