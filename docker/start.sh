#!/bin/bash
set -e

echo "==================================="
echo "Video Streaming Portal - Iniciando"
echo "==================================="

# Garantir permissões corretas no diretório temporário do PHP
echo "Configurando diretório temporário..."
mkdir -p /tmp
chmod 1777 /tmp
echo "✓ Diretório temporário configurado!"

# Criar arquivo .env se não existir (Coolify injeta vars como ENV, não arquivo)
if [ ! -f /var/www/html/.env ]; then
    echo "Criando arquivo .env a partir das variáveis de ambiente..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        # Criar .env mínimo - Laravel usará variáveis de ambiente do sistema
        cat > /var/www/html/.env << 'EOF'
APP_NAME="Video Streaming Portal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost
EOF
    fi
    echo "✓ Arquivo .env criado!"
fi

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

# Verificar se já foi instalado antes de fazer cache
if [ -f /var/www/html/storage/installed ]; then
    # Limpar e cachear configurações APENAS se já instalado
    echo "Otimizando aplicação..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    # Se não instalado, limpar cache para permitir instalador funcionar
    echo "Aplicação não instalada - limpando cache..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    php artisan cache:clear || true
fi

# Garantir permissões corretas
echo "Configurando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Garantir permissões de escrita para o instalador
chown www-data:www-data /var/www/html/.env
chmod 664 /var/www/html/.env
chown -R www-data:www-data /var/www/html/config
chmod -R 775 /var/www/html/config
chown -R www-data:www-data /var/www/html/public/install
chmod -R 775 /var/www/html/public/install

echo "==================================="
echo "✓ Aplicação iniciada com sucesso!"
echo "==================================="

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
