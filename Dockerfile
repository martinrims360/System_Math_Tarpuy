# Dockerfile
FROM php:8.2-apache

# Librerías de sistema necesarias para compilar pdo_pgsql (PostgreSQL)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensiones necesarias para conectar a Neon (PostgreSQL) vía PDO
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar el proyecto al servidor web
COPY . /var/www/html/

# Permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html

# Apache escucha en el puerto 80 dentro del contenedor
EXPOSE 80