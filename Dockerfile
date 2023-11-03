FROM php:8.2-cli


RUN apt-get clean \ 
    && apt-get update \
    && apt-get install -y ffmpeg \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY ./php.ini "$PHP_INI_DIR/php.ini"

COPY . .

RUN  composer install --no-dev

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]