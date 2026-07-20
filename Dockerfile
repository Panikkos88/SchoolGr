FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libonig-dev \
        libzip-dev \
    && docker-php-ext-install \
        mbstring \
        pdo_mysql \
        zip \
    && a2enmod headers rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-schoolgr.conf /etc/apache2/conf-available/schoolgr.conf
COPY docker/php-development.ini /usr/local/etc/php/conf.d/99-schoolgr-development.ini
COPY docker/web-entrypoint.sh /usr/local/bin/schoolgr-web-entrypoint

RUN a2enconf schoolgr \
    && sed -i 's/\r$//' /usr/local/bin/schoolgr-web-entrypoint \
    && chmod 0755 /usr/local/bin/schoolgr-web-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["schoolgr-web-entrypoint"]
CMD ["apache2-foreground"]
