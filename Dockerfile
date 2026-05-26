FROM php:8.1-apache

# Instalamos extensiones necesarias para bases de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitamos mod_rewrite de Apache para que funcionen los archivos .htaccess
RUN a2enmod rewrite

# Cambiamos el DocumentRoot para que apunte a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
