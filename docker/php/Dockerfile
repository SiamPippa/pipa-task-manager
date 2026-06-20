FROM php:8.4-fpm-alpine

COPY composer.json composer.lock* /var/www/html/

WORKDIR /var/www/html

RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    shadow \
    curl \
    git \
    openssh-client \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && apk del $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/* /tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

COPY --chown=www-data:www-data . /var/www/html
COPY docker/php/php.ini /usr/local/etc/php/

USER www-data

# Uncomment for production images (not needed if ./ is bind-mounted in dev):
# RUN COMPOSER_PROCESS_TIMEOUT=3000 composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]