FROM php:8.2.12-cli
RUN apt-get update && apt-get install -y unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update 2.8.4
WORKDIR /app
COPY composer.json composer.lock /app/
RUN composer install
COPY . /app
EXPOSE $PORT
CMD ["php", "-S", "0.0.0.0:$PORT"]
