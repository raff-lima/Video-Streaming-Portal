#!/bin/bash
set -e

echo "==================================="
echo "Video Streaming Portal - Iniciando"
echo "==================================="

# Aguardar banco de dados estar pronto
if [ -n "$DB_HOST" ]; then
    echo "Aguardando banco de dados em $DB_HOST:$DB_PORT..."
    timeout=60
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        timeout=$((timeout - 1))
        if [ $timeout -le 0 ]; then
            echo "ERRO: Timeout aguardando banco de dados!"
            exit 1
        fi
        echo "Aguardando conexão com banco de dados... ($timeout segundos restantes)"
        sleep 2
    done
    echo "✓ Banco de dados conectado!"
fi

# Gerar chave da aplicação se não existir
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --force
    echo "✓ APP_KEY gerada!"
fi

# Executar migrations (seguro, só roda se necessário)
echo "Verificando migrations..."
php artisan migrate --force || echo "⚠ Migrations já executadas ou erro"

# Criar link simbólico para storage (se não existir)
echo "Criando link simbólico para storage..."
php artisan storage:link || echo "⚠ Link já existe"

# Limpar e cachear configurações
echo "Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Garantir permissões corretas
echo "Configurando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==================================="
echo "✓ Aplicação iniciada com sucesso!"
echo "==================================="

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
