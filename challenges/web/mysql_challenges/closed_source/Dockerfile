FROM php:7.0-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN service apache2 restart

#COPY dist/public /var/www/html/
COPY dist/secret /tmp/secret

RUN docker-php-ext-configure /tmp/secret --enable-secret \
 && docker-php-ext-install /tmp/secret \
 && rm -r /tmp/secret

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 777 /var/www/html