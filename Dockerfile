FROM php:8.1-apache

# 1. Instalar dependências do sistema e extensões PHP necessárias para o CI4
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql intl

# 2. Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# 3. CORREÇÃO CRÍTICA: Mudar o DocumentRoot para /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Instalar Composer (necessário para o CI4)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Ajustar permissões iniciais
RUN chown -R www-data:www-data /var/www/html

# Garantir que o diretório writable (sessões, cache, logs) seja gravável pelo Apache
RUN mkdir -p /var/www/html/writable/session \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable
