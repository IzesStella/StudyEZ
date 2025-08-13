#!/usr/bin/env bash
set -e

echo "🚀 Iniciando StudyEZ..."

# Se o volume estiver vazio, copiar arquivos do build
if [ ! -f "/var/www/html/artisan" ]; then
    echo "📦 Copiando arquivos da aplicação para o volume..."
    cp -r /app/* /var/www/html/
    chown -R www-data:www-data /var/www/html
fi

# Verificar se .env existe, senão criar baseado no .env.example
# if [ ! -f .env ]; then
#     echo "📋 Criando .env baseado no .env.example..."
#     cp .env.example .env
# fi

# Ajustar configurações para Docker
# echo "🔧 Configurando ambiente Docker..."

# Database
# sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
# sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=password/' .env

# # Broadcasting e Pusher/WebSockets
# sed -i 's/BROADCAST_DRIVER=.*/BROADCAST_DRIVER=pusher/' .env
# sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env
# sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env
# sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=redis/' .env

# # Redis
# sed -i 's/REDIS_HOST=.*/REDIS_HOST=redis/' .env

# # Pusher/WebSockets - IMPORTANTE: usar as mesmas chaves
# sed -i 's/PUSHER_APP_ID=.*/PUSHER_APP_ID=minha-chave-app/' .env
# sed -i 's/PUSHER_APP_KEY=.*/PUSHER_APP_KEY=minha-chave-app/' .env
# sed -i 's/PUSHER_APP_SECRET=.*/PUSHER_APP_SECRET=minha-chave-secreta/' .env
# sed -i 's/PUSHER_HOST=.*/PUSHER_HOST=localhost/' .env
# sed -i 's/PUSHER_PORT=.*/PUSHER_PORT=6001/' .env
# sed -i 's/PUSHER_SCHEME=.*/PUSHER_SCHEME=http/' .env

# echo "✅ Configurações aplicadas!"

# Aguardar serviços
echo "🔍 Aguardando MySQL..."
DATABASE_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DATABASE_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
until nc -z -v -w30 $DATABASE_HOST $DATABASE_PORT; do
    echo "⚠️ MySQL não está pronto. Aguardando..."
    sleep 5
done

echo "🔍 Aguardando Redis..."
REDIS_HOST=$(grep REDIS_HOST .env | cut -d '=' -f2)
REDIS_PORT=$(grep REDIS_PORT .env | cut -d '=' -f2)
until nc -z -v -w30 $REDIS_HOST $REDIS_PORT; do
    echo "⚠️ Redis não está pronto. Aguardando..."
    sleep 5
done

# echo "✅ Serviços prontos!"

echo "🔑 Configurando Laravel..."

# Gerar APP_KEY se não existir
# if ! grep -q "APP_KEY=base64:" .env; then
#     php artisan key:generate 
# fi

# Limpar caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# Storage link
php artisan storage:link || true

# Migrations
echo "🗃️ Executando migrations..."
php artisan migrate || true

# Configurar permissões finais
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

echo "✅ StudyEZ configurado com sucesso!"

# Executar comando passado como argumento
exec "$@"
