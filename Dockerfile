FROM php:7.4-fpm

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring xml

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data: /var/www/html \
    && chmod -R 755 /var/www/html
    
EXPOSE 80

CMD service nginx start && php-fpm

    