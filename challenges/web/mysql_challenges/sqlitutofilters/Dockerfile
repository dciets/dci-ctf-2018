FROM php:7.0-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN service apache2 restart

COPY php.ini /usr/local/etc/php/
COPY public /var/www/html/

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html