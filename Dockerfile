FROM php:8.2-fpm

# Instalar dependências básicas (sem Node.js)
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    default-mysql-client \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install pdo_mysql zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código da aplicação
COPY . .

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Criar diretórios necessários
RUN mkdir -p storage/{app/public,framework/{cache,sessions,views},logs} bootstrap/cache

# Ajustar permissões
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]