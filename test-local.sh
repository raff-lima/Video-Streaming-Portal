#!/bin/bash

# Script de teste local antes do deploy no Coolify

echo "🧪 Testando Video Streaming Portal localmente..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando!"
    exit 1
fi

echo "✅ Docker está rodando"

# Build da imagem
echo "📦 Construindo imagem Docker..."
docker build -t video-streaming-portal:test .

if [ $? -ne 0 ]; then
    echo "❌ Erro no build!"
    exit 1
fi

echo "✅ Build concluído"

# Subir com docker-compose
echo "🚀 Iniciando containers..."
docker-compose up -d

if [ $? -ne 0 ]; then
    echo "❌ Erro ao iniciar containers!"
    exit 1
fi

echo "✅ Containers iniciados"
echo ""
echo "═══════════════════════════════════════"
echo "🌐 Aplicação disponível em:"
echo "   http://localhost:8080"
echo ""
echo "📊 Admin:"
echo "   http://localhost:8080/admin"
echo "   Email: admin@admin.com"
echo "   Senha: admin"
echo ""
echo "🗄️  MySQL:"
echo "   Host: localhost:3306"
echo "   Database: video_streaming"
echo "   User: root"
echo "   Pass: secret"
echo "═══════════════════════════════════════"
echo ""
echo "📋 Comandos úteis:"
echo "   docker-compose logs -f app    # Ver logs"
echo "   docker-compose down           # Parar tudo"
echo "   docker-compose exec app bash  # Acessar container"
echo ""
