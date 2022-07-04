FROM php:8.0.20-apache
RUN apt-get update; \
    apt-get upgrade -y;

RUN docker-php-ext-install mysqli

COPY cors.conf /etc/apache2/conf-enabled/cors.conf
RUN a2enmod headers; \
    a2enmod rewrite; \
    service apache2 restart;