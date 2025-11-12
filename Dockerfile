# Etapa 1: Instalar dependencias de PHP y Composer
FROM php:8.2-fpm

# Instalar extensiones necesarias para Laravel y MySQL
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www

# Copiar los archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Dar permisos a Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto del servidor Laravel
EXPOSE 8000

# Comando de inicio (usa php artisan serve)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

