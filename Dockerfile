# Dockerfile
FROM php:8.2-apache

# Extensiones necesarias para conectar a Supabase (PostgreSQL) vía PDO
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar el proyecto al servidor web
COPY . /var/www/html/

# Permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html

# Apache escucha en el puerto 80 dentro del contenedor
EXPOSE 80
