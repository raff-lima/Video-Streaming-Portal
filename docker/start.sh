#!/bin/bash
set -e

echo "🚀 Iniciando Video Streaming Portal..."

# Criar diretórios de log se não existirem
mkdir -p /var/log/nginx
mkdir -p /var/log

# Aguardar banco de dados estar pronto (com timeout)
echo "⏳ Aguardando banco de dados..."
max_attempts=60
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Banco de dados conectado!"
        break
    fi
    echo "Tentativa $((attempt + 1)) de $max_attempts..."
    attempt=$((attempt + 1))
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "⚠️  Timeout ao conectar no banco de dados, continuando mesmo assim..."
fi

# Rodar migrations (apenas se necessário)
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "📦 Rodando migrations..."
    php artisan migrate --force --no-interaction 2>&1 || echo "⚠️  Migrations já executadas ou erro"
fi

# Criar link simbólico do storage
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Criando link simbólico do storage..."
    php artisan storage:link 2>&1 || echo "⚠️  Link já existe"
fi

# Limpar caches antigos
echo "🧹 Limpando caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Cachear configurações para produção
if [ "${APP_ENV}" = "production" ]; then
    echo "⚡ Cacheando configurações..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Ajustar permissões finais
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/upload 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/upload 2>/dev/null || true

echo "✨ Aplicação pronta!"
echo "🌐 Servidor iniciando na porta 80..."

# Iniciar Supervisor (que iniciará PHP-FPM e Nginx)
exec /usr/bin/supervisord -c /etc/supervisord.conf
