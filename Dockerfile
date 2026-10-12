FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    libzip-dev libsqlite3-dev sqlite3 zip unzip libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN a2enmod rewrite
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

ENV PORT 80
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
EXPOSE ${PORT}

CMD touch /var/www/html/database/database.sqlite && \
    chown www-data:www-data /var/www/html/database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    apache2-foreground
