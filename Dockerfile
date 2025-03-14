# Usar una imagen base con PHP 8.2 y FPM
FROM php:8.2-fpm

# Instalar dependencias necesarias para Nginx y PHP
RUN apt-get update && apt-get install -y \
    nginx \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring zip opcache

# Copiar la configuración personalizada de Nginx
COPY conf/nginx.conf /etc/nginx/nginx.conf

# Copiar configuración personalizada de PHP (si tienes un archivo php.ini)
# COPY conf/php.ini /usr/local/etc/php/php.ini

# Crear directorios necesarios
RUN mkdir -p /var/www/html

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Copiar tu aplicación PHP al contenedor
COPY index.php /var/www/html/

# Exponer puertos para Nginx
EXPOSE 80

# Iniciar tanto Nginx como PHP-FPM
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
