#!/bin/bash
set -e

echo "🚀 Iniciando Video Streaming Portal..."

# Aguardar banco de dados estar pronto
echo "⏳ Aguardando banco de dados..."
max_attempts=30
attempt=0
until php artisan db:show > /dev/null 2>&1 || [ $attempt -eq $max_attempts ]; do
    echo "Tentativa $attempt de $max_attempts..."
    attempt=$((attempt + 1))
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "❌ Não foi possível conectar ao banco de dados"
    exit 1
fi

echo "✅ Banco de dados conectado!"

# Rodar migrations (apenas se necessário)
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "📦 Rodando migrations..."
    php artisan migrate --force --no-interaction || echo "⚠️  Migrations já executadas ou erro"
fi

# Criar link simbólico do storage
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando link simbólico do storage..."
    php artisan storage:link || echo "⚠️  Link já existe"
fi

# Limpar e cachear configurações para produção
echo "🧹 Otimizando aplicação..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "⚡ Cacheando configurações..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoload do Composer
echo "📚 Otimizando autoload..."
composer dump-autoload --optimize --no-dev

# Ajustar permissões finais
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✨ Aplicação pronta!"
echo "🌐 Servidor iniciando na porta 80..."

# Iniciar Supervisor (que iniciará PHP-FPM e Nginx)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
