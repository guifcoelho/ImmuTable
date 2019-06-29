FROM php:7.3

RUN apt-get update && apt-get install -y --fix-missing \
    git \
    zip \
    curl \
    unzip \
    libzip-dev

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
    zip \
    mbstring

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini    

# Instala o composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer