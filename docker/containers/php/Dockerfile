FROM php:8.2-apache

RUN apt-get update && apt-get install -y vim \
    && apt-get install -y curl ca-certificates zip dbus libpng-dev libzip-dev zlib1g-dev \
    # Install php ext, dependencies
    && apt-get install -y default-mysql-client mariadb-client \
    && docker-php-ext-install opcache exif mysqli pdo_mysql zip pcntl sockets;

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ssl /etc/apache2/ssl

COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod ssl
RUN a2enmod rewrite
RUN a2enmod headers

WORKDIR /var/www/html
