FROM php:7.0-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite
RUN service apache2 restart

RUN adduser --force-badname DCI-b1631cca3391f3f

COPY php.ini /usr/local/etc/php/
COPY public /var/www/html/

RUN chown -R root:www-data /var/www/html
RUN chmod -R 1755 /var/www/html
RUN find /var/www/html -type d -exec chmod 1775 {} +