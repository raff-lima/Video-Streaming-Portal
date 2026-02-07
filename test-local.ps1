# Script de teste local para Windows

Write-Host "🧪 Testando Video Streaming Portal localmente..." -ForegroundColor Cyan

# Verificar se Docker está rodando
try {
    docker info | Out-Null
    Write-Host "✅ Docker está rodando" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker não está rodando!" -ForegroundColor Red
    exit 1
}

# Build da imagem
Write-Host "📦 Construindo imagem Docker..." -ForegroundColor Yellow
docker build -t video-streaming-portal:test .

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro no build!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Build concluído" -ForegroundColor Green

# Subir com docker-compose
Write-Host "🚀 Iniciando containers..." -ForegroundColor Yellow
docker-compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro ao iniciar containers!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Containers iniciados" -ForegroundColor Green
Write-Host ""
Write-Host "═══════════════════════════════════════" -ForegroundColor Cyan
Write-Host "🌐 Aplicação disponível em:" -ForegroundColor White
Write-Host "   http://localhost:8080" -ForegroundColor Yellow
Write-Host ""
Write-Host "📊 Admin:" -ForegroundColor White
Write-Host "   http://localhost:8080/admin" -ForegroundColor Yellow
Write-Host "   Email: admin@admin.com" -ForegroundColor Gray
Write-Host "   Senha: admin" -ForegroundColor Gray
Write-Host ""
Write-Host "🗄️  MySQL:" -ForegroundColor White
Write-Host "   Host: localhost:3306" -ForegroundColor Gray
Write-Host "   Database: video_streaming" -ForegroundColor Gray
Write-Host "   User: root" -ForegroundColor Gray
Write-Host "   Pass: secret" -ForegroundColor Gray
Write-Host "═══════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "📋 Comandos úteis:" -ForegroundColor White
Write-Host "   docker-compose logs -f app    # Ver logs" -ForegroundColor Gray
Write-Host "   docker-compose down           # Parar tudo" -ForegroundColor Gray
Write-Host "   docker-compose exec app bash  # Acessar container" -ForegroundColor Gray
Write-Host ""
