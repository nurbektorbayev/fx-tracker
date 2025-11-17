FROM php:8.3-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libpq-dev

# PHP extensions
RUN docker-php-ext-install \
    intl \
    pdo \
    pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install composer dependencies
COPY composer.json composer.lock symfony.* ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

# Copy source
COPY . .

RUN composer dump-autoload --optimize \
 && php bin/console cache:clear --env=prod --no-debug

CMD ["php-fpm"]
