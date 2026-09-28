FROM php:8.4-apache

ARG HOST_UID=1000
ARG HOST_GID=1000

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# ---------------------------------------------------------
# System dependencies
# ---------------------------------------------------------

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    nano \
    procps \
    sudo \
    libicu-dev \
    libxml2-dev \
    libreadline-dev \
    libzip-dev \
    libpq-dev \
    postgresql-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# PHP extensions
# ---------------------------------------------------------

RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-webp \
    --with-freetype \
    && docker-php-ext-install \
    bcmath \
    exif \
    gd \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo_pgsql \
    sockets \
    zip

# ---------------------------------------------------------
# Xdebug
# ---------------------------------------------------------

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# ---------------------------------------------------------
# Node.js 22 LTS
# ---------------------------------------------------------

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm --version \
    && node --version \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# Apache
# ---------------------------------------------------------

RUN a2enmod rewrite headers

# ---------------------------------------------------------
# Match container www-data UID/GID to host
# ---------------------------------------------------------

RUN groupmod -o -g ${HOST_GID} www-data \
    && usermod -o -u ${HOST_UID} -g ${HOST_GID} www-data

# ---------------------------------------------------------
# Apache configuration
# ---------------------------------------------------------

COPY apache/000-default.conf \
    /etc/apache2/sites-available/000-default.conf

# ---------------------------------------------------------
# PHP configuration
# ---------------------------------------------------------

COPY php/php.ini \
    /usr/local/etc/php/php.ini

# ---------------------------------------------------------
# Composer
# ---------------------------------------------------------

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------
# Laravel
# ---------------------------------------------------------

WORKDIR /var/www/html

# ---------------------------------------------------------
# Permissions
# ---------------------------------------------------------

RUN mkdir -p /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

EXPOSE 80