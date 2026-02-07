# ═══════════════════════════════════════════════════════════════

# CHECKLIST DE PREPARAÇÃO PARA DEPLOY NO COOLIFY

# ═══════════════════════════════════════════════════════════════

## 📋 ANTES DE SUBIR PARA O GIT

### Arquivos Criados ✅

- [ ] Dockerfile
- [ ] .dockerignore
- [ ] docker/nginx.conf
- [ ] docker/supervisord.conf
- [ ] docker/start.sh
- [ ] docker/healthcheck.sh
- [ ] .env.production (template)
- [ ] docker-compose.yml (teste local)
- [ ] DEPLOY_COOLIFY.md
- [ ] LEIA-ME.txt
- [ ] .gitignore (atualizado)

### Verificações de Código

- [ ] Remover senhas hardcoded
- [ ] Verificar se .env está em .gitignore
- [ ] Verificar se vendor/ está em .gitignore
- [ ] Verificar se node_modules/ está em .gitignore
- [ ] Verificar permissões dos scripts (.sh devem ser executáveis)

### Arquivos Sensíveis (NÃO COMMITAR!)

- [ ] .env (remover ou garantir que está no .gitignore)
- [ ] .env.backup
- [ ] /storage/logs/\*.log
- [ ] /vendor (será instalado no build)
- [ ] /node_modules

---

## 🔧 NO COOLIFY

### 1. Criar Database

- [ ] Acessar Coolify > Databases
- [ ] Criar MySQL 8.0
- [ ] Nome: video_streaming_db
- [ ] Anotar credenciais (host, user, password, database)

### 2. Criar Aplicação

- [ ] Coolify > New Application
- [ ] Conectar repositório Git
- [ ] Branch: main
- [ ] Build Pack: Docker (detectado automaticamente)
- [ ] Port: 80

### 3. Configurar Environment Variables

Copiar de .env.production e configurar:

#### Essenciais ⚠️

- [ ] APP_NAME
- [ ] APP_ENV=production
- [ ] APP_KEY= (deixar vazio por enquanto, gerar depois)
- [ ] APP_DEBUG=false
- [ ] APP_URL=https://seudominio.com

#### Database 🗄️

- [ ] DB_CONNECTION=mysql
- [ ] DB_HOST= (do MySQL criado)
- [ ] DB_PORT=3306
- [ ] DB_DATABASE= (nome do banco)
- [ ] DB_USERNAME= (user do banco)
- [ ] DB_PASSWORD= (senha do banco)

#### Outros

- [ ] APP_TIMEZONE=America/Sao_Paulo
- [ ] APP_LANG=pt
- [ ] RUN_MIGRATIONS=true (primeira vez)

#### Email (configurar depois) 📧

- [ ] MAIL_MAILER=smtp
- [ ] MAIL_HOST=
- [ ] MAIL_PORT=587
- [ ] MAIL_USERNAME=
- [ ] MAIL_PASSWORD=
- [ ] MAIL_FROM_ADDRESS=

### 4. Configurar Domínio

- [ ] Coolify > Domains
- [ ] Adicionar domínio
- [ ] Ativar SSL (Let's Encrypt)
- [ ] Aguardar propagação DNS

### 5. Configurar Storage Persistente

- [ ] Coolify > Storages
- [ ] Adicionar volume: /var/www/html/storage/app
- [ ] Adicionar volume: /var/www/html/public/upload

---

## 🚀 DEPLOY

### Primeiro Deploy

- [ ] Clicar em "Deploy"
- [ ] Acompanhar logs
- [ ] Aguardar conclusão (5-10 min)

### Após Deploy

#### Via Terminal do Container:

1. Gerar APP_KEY

```bash
php artisan key:generate --show
```

- [ ] Copiar key gerada (começa com base64:)
- [ ] Adicionar em Environment Variables > APP_KEY
- [ ] Restart da aplicação

2. Verificar Migrations

```bash
php artisan migrate:status
```

- [ ] Se não rodou, executar: `php artisan migrate --force`

3. Criar Storage Link

```bash
php artisan storage:link
```

4. Otimizar

```bash
php artisan optimize
```

5. Testar aplicação

- [ ] Acessar homepage
- [ ] Acessar /admin
- [ ] Login com admin@admin.com / admin
- [ ] MUDAR SENHA DO ADMIN!

---

## ✅ PÓS-DEPLOY

### Configurações Iniciais

- [ ] Settings > General > Configurar site (logo, nome, etc)
- [ ] Settings > SMTP > Configurar email
- [ ] Settings > Social Login > Configurar (se necessário)
- [ ] Payment Gateway > Configurar gateways
- [ ] Adicionar conteúdo de teste

### Segurança

- [ ] Mudar senha do admin
- [ ] Configurar backup no Coolify
- [ ] Testar uploads
- [ ] Testar videos (YouTube, embed, etc)
- [ ] Verificar SSL está funcionando

### Otimizações

- [ ] Configurar CDN (se necessário)
- [ ] Configurar cache (Redis/Memcached se disponível)
- [ ] Ajustar php.ini limits se necessário
- [ ] Monitorar uso de recursos

---

## 🐛 TROUBLESHOOTING

### Build Falhou

- [ ] Verificar logs do build
- [ ] Verificar Dockerfile
- [ ] Verificar se todos arquivos estão no Git
- [ ] Tentar rebuild

### Erro 500

- [ ] Verificar APP_KEY está configurada
- [ ] Verificar conexão com banco
- [ ] Ver logs: docker logs CONTAINER_ID
- [ ] Ver Laravel logs no container

### Banco Não Conecta

- [ ] Verificar credenciais do DB\_\*
- [ ] Verificar se MySQL está rodando
- [ ] Testar conexão manual
- [ ] Verificar DB_HOST (deve ser o service name do MySQL)

### Upload Não Funciona

- [ ] Verificar storage link: `php artisan storage:link`
- [ ] Verificar permissões: `chmod -R 775 storage`
- [ ] Verificar volumes persistentes configurados
- [ ] Verificar client_max_body_size no nginx

---

## 📞 ACESSO PADRÃO

### Frontend

```
URL: https://seudominio.com
```

### Admin

```
URL: https://seudominio.com/admin
Email: admin@admin.com
Senha: admin
```

⚠️ **MUDAR IMEDIATAMENTE!**

---

## 🎉 COMPLETO!

- [ ] Aplicação está online e funcionando
- [ ] SSL configurado
- [ ] Backup configurado
- [ ] Monitoramento configurado
- [ ] Documentado para equipe

---

📖 Para mais detalhes: DEPLOY_COOLIFY.md
