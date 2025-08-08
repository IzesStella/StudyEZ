# --- Estágio do Composer ---
FROM php:8.2-fpm AS composer-stage

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

WORKDIR /app
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts

# --- Build do Frontend ---
FROM node:20 AS build-stage

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
COPY --from=composer-stage /app/vendor ./vendor

# Build do projeto
RUN npm run build

# --- Produção ---
FROM php:8.2-fpm

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Copiar dependências do Composer e Node.js dos estágios anteriores
COPY --from=composer-stage /app/vendor ./vendor
COPY --from=build-stage /app/public/build ./public/build

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