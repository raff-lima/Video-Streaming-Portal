# 🚀 Deploy no Coolify - Video Streaming Portal

## 📋 Pré-requisitos

### 1. Subir código para Git

```bash
# Inicializar repositório Git
cd "video_streaming_portal"
git init
git add .
git commit -m "Initial commit - Video Streaming Portal"

# Criar repositório no GitHub/GitLab e fazer push
git remote add origin SEU_REPOSITORIO_GIT
git branch -M main
git push -u origin main
```

---

## 🔧 Configuração no Coolify

### 1. Criar MySQL Database

1. Acesse seu Coolify
2. Vá em **Databases** → **Add New**
3. Escolha **MySQL 8.0**
4. Nome: `video_streaming_db`
5. Anote as credenciais geradas

### 2. Criar Aplicação

1. Vá em **Applications** → **Add New**
2. Conecte seu repositório Git
3. Escolha a branch: `main`
4. **Build Pack:** Docker (vai detectar o Dockerfile automaticamente)

### 3. Configurar Variáveis de Ambiente

Vá em **Environment Variables** e adicione:

```bash
# Aplicação
APP_NAME=Video Streaming Portal
APP_ENV=production
APP_KEY=base64:GERAR_DEPOIS
APP_DEBUG=false
APP_URL=https://seudominio.com

APP_TIMEZONE=America/Sao_Paulo
APP_LANG=pt

LOG_CHANNEL=stack
LOG_LEVEL=error

# Banco de Dados (use as credenciais do MySQL criado no Coolify)
DB_CONNECTION=mysql
DB_HOST=video_streaming_db
DB_PORT=3306
DB_DATABASE=video_streaming
DB_USERNAME=root
DB_PASSWORD=SUA_SENHA_DO_MYSQL

# Cache & Session
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Migrations (primeira vez apenas)
RUN_MIGRATIONS=true

# Email (configurar depois)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME=Video Streaming Portal
```

### 4. Configurar Domínio

1. Vá em **Domains**
2. Adicione seu domínio
3. Ative **SSL (Let's Encrypt)**

### 5. Configurar Storage (Persistente)

1. Vá em **Storages/Volumes**
2. Adicione volumes para dados persistentes:

```
Source: /var/www/html/storage/app
Destination: storage_app

Source: /var/www/html/public/upload
Destination: uploads
```

---

## 🎬 Deploy

### 1. Primeiro Deploy

1. Clique em **Deploy**
2. Aguarde o build (pode levar 5-10 minutos)
3. Verifique os logs

### 2. Após Deploy - Gerar APP_KEY

Execute no terminal do container:

```bash
# Gerar APP_KEY
php artisan key:generate --show

# Copie a key gerada e adicione na variável APP_KEY no Coolify
# Formato: base64:XXXXXXXXXXXXXXXXXXXXXXXX
```

Depois de adicionar a APP_KEY:

- Atualize a variável no Coolify
- Faça um **Restart** da aplicação

### 3. Rodar Migrations (Se RUN_MIGRATIONS=false)

Se você configurou `RUN_MIGRATIONS=false`, rode manualmente:

```bash
php artisan migrate --force
```

### 4. Criar Admin (Terminal do Container)

```bash
# Conectar ao container via terminal do Coolify e executar
php artisan db:seed
# Ou criar manualmente no banco
```

---

## 🔧 Comandos Úteis (Terminal do Container)

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link do storage
php artisan storage:link

# Ver status do banco
php artisan db:show

# Rodar migrations
php artisan migrate --force

# Verificar logs
tail -f /var/www/html/storage/logs/laravel.log
```

---

## 📊 Acessar a Aplicação

### Frontend

```
https://seudominio.com
```

### Admin

```
https://seudominio.com/admin

Email: admin@admin.com
Senha: admin
```

⚠️ **IMPORTANTE:** Mude a senha do admin imediatamente após o primeiro acesso!

---

## 🐛 Troubleshooting

### Build falha

- Verifique os logs do build no Coolify
- Certifique-se que todos os arquivos estão no Git
- Verifique o Dockerfile

### Erro 500

- Verifique se APP_KEY está configurada
- Verifique conexão com banco de dados
- Veja os logs: `storage/logs/laravel.log`

### Upload não funciona

- Verifique se o volume de storage está configurado
- Execute: `php artisan storage:link`
- Verifique permissões: `chmod -R 775 storage`

### Banco não conecta

- Verifique se o service name do MySQL está correto em DB_HOST
- Teste a conexão manualmente
- Verifique se o MySQL está rodando

---

## 🔄 Atualizações

Para atualizar o código:

1. Faça commit das mudanças no Git
2. Push para o repositório
3. No Coolify, clique em **Deploy** ou configure auto-deploy

---

## 📞 Credenciais Padrão (MUDAR IMEDIATAMENTE!)

```
Admin:
Email: admin@admin.com
Senha: admin
```

---

## ✅ Checklist Pós-Deploy

- [ ] APP_KEY gerada e configurada
- [ ] Domínio configurado com SSL
- [ ] Banco de dados conectado
- [ ] Migrations executadas
- [ ] Storage link criado
- [ ] Volumes persistentes configurados
- [ ] Senha do admin alterada
- [ ] SMTP configurado (opcional)
- [ ] Backup configurado (recomendado)
- [ ] Logs monitorados

---

## 🎉 Pronto!

Sua aplicação está no ar! Acesse e configure:

1. Informações do site (Admin > Settings)
2. Adicionar conteúdo
3. Configurar payments gateways
4. Configurar emails
