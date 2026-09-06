FROM php:5.6.40-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# La aplicación usa reglas .htaccess y necesita que Apache permita overrides.
COPY docker/php.ini /usr/local/etc/php/conf.d/ayudas.ini
