#!/usr/bin/env bash
set -e

echo "🚀 Iniciando StudyEZ..."

# SEMPRE recriar .env baseado no .env.example para Docker
echo "📋 Recriando .env baseado no .env.example..."
cp .env.example .env

# Ajustar configurações para Docker
echo "🔧 Ajustando configurações para Docker..."

# Database - manter usuário root, só mudar host e senha
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/' .env
sed -i 's/DB_PASSWORD=root/DB_PASSWORD=password/' .env

# App URL para porta 8000
sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8000|' .env

# Environment para debug temporário
sed -i 's/APP_ENV=local/APP_ENV=local/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=true/' .env

# Configurar Broadcaster para usar o WebSockets
sed -i 's/BROADCAST_DRIVER=log/BROADCAST_DRIVER=pusher/' .env
sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=redis/' .env
sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env

sed -i 's/PUSHER_APP_ID=/PUSHER_APP_ID=minha-chave-app-id/' .env
sed -i 's/PUSHER_APP_KEY=/PUSHER_APP_KEY=minha-chave-app/' .env
sed -i 's/PUSHER_APP_SECRET=/PUSHER_APP_SECRET=minha-chave-app-secret/' .env
sed -i 's/PUSHER_HOST=/PUSHER_HOST=websockets/' .env
sed -i 's/PUSHER_PORT=443/PUSHER_PORT=6001/' .env
sed -i 's/PUSHER_SCHEME=https/PUSHER_SCHEME=http/' .env

echo "✅ Arquivo .env configurado para Docker!"

# Esperar até que o MySQL esteja pronto
echo "🔍 Aguardando o MySQL..."
until nc -z -v -w30 mysql 3306
do
  echo "⚠️ MySQL não está pronto. Aguardando..."
  sleep 5
done
echo "✅ MySQL está pronto!"

# Configurar aplicação
echo "🔑 Configurando Laravel..."

# Gerar nova APP_KEY
echo "Gerando nova APP_KEY..."
php artisan key:generate --force --no-interaction

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Storage link ANTES de copiar arquivos
echo "🔗 Criando storage link..."
php artisan storage:link --force 2>/dev/null || true

# Copiar arquivos públicos para o volume compartilhado (incluindo build E storage)
echo "📁 Sincronizando arquivos públicos..."
if [ -d "/var/www/html/public/build" ]; then
    cp -r /var/www/html/public/* /var/www/html/public/ 2>/dev/null || true
    echo "✅ Arquivos de build copiados!"
else
    echo "⚠️ Pasta build não encontrada"
fi

# Garantir que o link do storage existe no volume compartilhado
if [ -L "/var/www/html/public/storage" ]; then
    echo "🔗 Link do storage já existe!"
else
    echo "🔗 Criando link do storage no volume..."
    ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
fi

# Executando migrations - agora usando root com senha password
echo "🗃️ Executando migrations..."
php artisan migrate --force --no-interaction

# Permissões - incluir storage/app/public
echo "🔧 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

echo "✅ StudyEZ configurado! Iniciando PHP-FPM..."
exec php-fpm
