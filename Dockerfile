# Базовый образ с PHP и Apache
FROM php:8.2-apache

# Устанавливаем зависимости для PHP
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Включаем модуль pdo и pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Включаем модуль mod_rewrite для Apache
RUN a2enmod rewrite

# Копируем composer из официального образа
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Изменяем права доступа к директории
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем конфигурацию Apache
COPY apache.conf /etc/apache2/conf-available/myapp.conf
RUN a2enconf myapp

# Устанавливаем ServerName в Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
