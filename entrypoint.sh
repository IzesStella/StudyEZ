#!/usr/bin/env bash
set -e

echo "🚀 Iniciando StudyEZ..."

# 1) Usar configuração Docker (opcional, mas recomendado)
if [ -f .env.docker ]; then
    echo "📋 Copiando .env.docker para .env..."
    cp .env.docker .env
fi

# 2) Aguardar MySQL estar completamente pronto
echo "🔍 Aguardando MySQL..."
until mysqladmin ping -h mysql --silent 2>/dev/null; do
    echo "⏳ MySQL ainda não está pronto..."
    sleep 3
done

sleep 5
echo "✅ MySQL conectado!"

# 3) Configurar aplicação
echo "🔑 Configurando Laravel..."

# Gerar APP_KEY se necessário
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# 4) Storage link (importante para uploads)
echo "🔗 Criando storage link..."
php artisan storage:link --force 2>/dev/null || true

# 5) Migrations
echo "🗃️ Executando migrations..."
php artisan migrate --force --no-interaction

# 6) Cache
echo "🧹 Configurando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7) Permissões para uploads
echo "🔧 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

echo "✅ StudyEZ configurado! Iniciando PHP-FPM..."
exec php-fpm