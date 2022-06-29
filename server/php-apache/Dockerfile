FROM php:8.0.20-apache
RUN apt-get update; \
    apt-get upgrade -y;

RUN docker-php-ext-install mysqli

COPY demo.apache.conf /usr/local/apache2/conf/demo.apache.conf
RUN echo "Include /usr/local/apache2/conf/demo.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf