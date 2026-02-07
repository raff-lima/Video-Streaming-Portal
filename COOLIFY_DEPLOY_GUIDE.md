# 🚀 Guia Completo: Deploy Video Streaming Portal no Coolify

## 📋 Visão Geral do Projeto

- **Tecnologia:** Laravel 10
- **PHP:** >= 8.2
- **Banco de Dados:** MySQL/MariaDB
- **Servidor Web:** Apache/Nginx (gerenciado pelo Coolify via Traefik)

---

## 🎯 Passo 1: Preparar o Projeto para Deploy

### 1.1 Criar Dockerfile

Crie um arquivo `Dockerfile` na raiz do projeto:

```dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    mysql-client \
    bash \
    git

# Instalar extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Instalar dependências do Composer (sem dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Configurar Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Criar diretórios necessários
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Expor porta 80
EXPOSE 80

# Comando de inicialização
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### 1.2 Criar Configurações do Nginx

Crie a pasta `docker` na raiz e adicione os arquivos:

**docker/nginx.conf:**

```nginx
user www-data;
worker_processes auto;
pid /run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log warn;

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 100M;

    gzip on;
    gzip_disable "msie6";

    include /etc/nginx/http.d/*.conf;
}
```

**docker/default.conf:**

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        access_log off;
    }
}
```

**docker/supervisord.conf:**

```ini
[supervisord]
nodaemon=true
loglevel=info
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
priority=5
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g 'daemon off;'
autostart=true
autorestart=true
priority=10
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

### 1.3 Criar .dockerignore

```
.git
.env
.env.example
node_modules
vendor
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
bootstrap/cache/*
.DS_Store
Thumbs.db
```

---

## 🌐 Passo 2: Configurar Git Repository

### 2.1 Inicializar Git (se ainda não tiver)

```bash
cd "video_streaming_portal"
git init
git add .
git commit -m "Initial commit - Video Streaming Portal"
```

### 2.2 Criar Repositório Remoto

Opções:

- **GitHub:** https://github.com/new
- **GitLab:** https://gitlab.com/projects/new
- **Bitbucket:** https://bitbucket.org/repo/create

```bash
# Adicionar remote (exemplo GitHub)
git remote add origin https://github.com/seu-usuario/video-streaming-portal.git
git branch -M main
git push -u origin main
```

---

## ☁️ Passo 3: Configurar Coolify

### 3.1 Acessar Coolify

1. Acesse seu Coolify: `http://SEU_IP:8000`
2. Faça login com suas credenciais

### 3.2 Conectar Servidor (se necessário)

Se ainda não conectou um servidor:

1. Vá em **Servers** → **Add Server**
2. Configure SSH
3. Teste conexão

### 3.3 Criar Banco de Dados MySQL

1. Vá em **+ New Resource** → **Database**
2. Escolha **MySQL** (versão 8.0 ou superior)
3. Configure:
    - **Name:** videoportal-db
    - **MySQL Root Password:** [senha-segura]
    - **Database Name:** videoportal
    - **Username:** videoadmin
    - **Password:** [senha-do-usuario]
4. Clique em **Deploy**
5. Aguarde o banco estar **running**
6. **IMPORTANTE:** Anote o hostname interno (ex: `videoportal-db.coolify.local`)

---

## 🚀 Passo 4: Deploy da Aplicação no Coolify

### 4.1 Criar Nova Aplicação

1. Vá em **+ New Resource** → **Application**
2. Escolha **Git Repository**
3. Configure:

**Source:**

- **Repository:** `https://github.com/seu-usuario/video-streaming-portal`
- **Branch:** `main`
- **Build Pack:** **Dockerfile** (ou "Docker" - NÃO escolha "Docker Compose")

**General:**

- **Name:** video-streaming-portal
- **Port:** `80`

> ⚠️ **IMPORTANTE:** Escolha a opção que usa **Dockerfile**, não "Docker Compose".
> Criamos um Dockerfile único, não um docker-compose.yml

**Domain:**

- Configure seu domínio: `videoportal.seudominio.com`
- Ou use o wildcard gerado automaticamente

### 4.2 Configurar Variáveis de Ambiente

Na seção **Environment Variables**, adicione:

```env
# Application
APP_NAME=VideoStreamingPortal
APP_ENV=production
APP_DEBUG=false
APP_URL=https://videoportal.seudominio.com

# Timezone e Idioma
APP_TIMEZONE=America/Sao_Paulo
APP_LANG=pt

# Database (use o hostname interno do Coolify)
DB_CONNECTION=mysql
DB_HOST=videoportal-db.coolify.local
DB_PORT=3306
DB_DATABASE=videoportal
DB_USERNAME=videoadmin
DB_PASSWORD=sua-senha-aqui

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (configurar depois)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Log
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**IMPORTANTE:** O `APP_KEY` será gerado automaticamente ou você pode gerar com:

```bash
php artisan key:generate --show
```

### 4.3 Modificar Dockerfile para Produção

Atualize o Dockerfile para incluir comandos de inicialização:

```dockerfile
# ... (código anterior igual)

# Script de inicialização
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
```

**docker/start.sh:**

```bash
#!/bin/bash

# Aguardar banco de dados estar pronto
echo "Aguardando banco de dados..."
until nc -z -v -w30 $DB_HOST $DB_PORT
do
    echo "Aguardando conexão com banco de dados..."
    sleep 5
done
echo "Banco de dados conectado!"

# Gerar chave da aplicação se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Executar migrations (apenas na primeira vez)
php artisan migrate --force

# Limpar e cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico para storage
php artisan storage:link

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

---

## 🎬 Passo 5: Primeira Instalação (Instalador Web)

### 5.1 Desabilitar Instalador após Setup

A aplicação tem um instalador web em `/install`. Após a primeira instalação:

1. Acesse: `https://videoportal.seudominio.com/install`
2. Complete todos os passos do instalador
3. Após finalizar, **remover/desabilitar** a rota de instalação

### 5.2 Alternativa: Migrations Manuais

Se preferir pular o instalador web:

```bash
# Conectar ao container via Coolify Terminal
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminSeeder
```

---

## 🔒 Passo 6: Configurações de Segurança

### 6.1 Variáveis Sensíveis

Nunca commite no Git:

- Arquivo `.env` (já está no .gitignore)
- Chaves de API
- Senhas

### 6.2 SSL/TLS

O Coolify configura automaticamente SSL via Let's Encrypt quando você:

1. Usa `https://` no domínio
2. DNS está apontando corretamente para o servidor

---

## 📊 Passo 7: Configurações Pós-Deploy

### 7.1 Acessar Admin

```
URL: https://videoportal.seudominio.com/admin
Email: admin@admin.com
Senha: admin
```

**IMPORTANTE:** Altere a senha padrão imediatamente!

### 7.2 Configurar no Admin Panel

1. **Settings → General Settings**
    - Logo, Favicon
    - Nome do site
    - Timezone: America/Sao_Paulo

2. **Settings → SMTP Email**
    - Configure Gmail ou seu provedor

3. **Settings → Social Login**
    - Google, Facebook (se usar)

4. **Payment Gateway**
    - PayPal, Stripe, Razorpay, etc.

### 7.3 Configurar TMDB API

Para informações de filmes/séries:

1. Crie conta em https://www.themoviedb.org/
2. Obtenha API Key
3. Configure em: **Admin → Settings → General → API Read Access Token**

---

## 🔄 Passo 8: Atualizações e Manutenção

### 8.1 Deploy de Atualizações

1. Faça alterações no código local
2. Commit e push:
    ```bash
    git add .
    git commit -m "Atualização X"
    git push origin main
    ```
3. No Coolify: Clique em **Redeploy**

### 8.2 Backup Automático

Configure no Coolify:

1. Vá em **Database** → **Backups**
2. Configure backup para S3 compatível
3. Defina frequência (diária recomendada)

### 8.3 Monitoramento

No Coolify você pode:

- Ver logs em tempo real
- Monitorar uso de recursos
- Receber alertas

---

## 🐛 Troubleshooting

### Problema: Erro 500

```bash
# Ver logs no Coolify
# Ou conectar ao container:
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problema: Permissões

```bash
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
```

### Problema: Banco não conecta

Verifique:

1. Hostname correto (ex: `videoportal-db.coolify.local`)
2. Porta correta (3306)
3. Credenciais corretas
4. Banco de dados criado

### Problema: Upload de arquivos

Aumente limites no Dockerfile:

```dockerfile
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini
```

---

## 📝 Checklist Final

- [ ] Git repository criado e commitado
- [ ] Dockerfile e configurações criadas
- [ ] Banco MySQL criado no Coolify
- [ ] Aplicação criada no Coolify
- [ ] Variáveis de ambiente configuradas
- [ ] Domínio configurado e DNS apontado
- [ ] Deploy realizado com sucesso
- [ ] Instalador web executado ou migrations rodadas
- [ ] Admin acessível e senha alterada
- [ ] SSL funcionando (https)
- [ ] SMTP configurado e testado
- [ ] Backup automático configurado

---

## 🆘 Suporte

**Documentação Original:** Ver arquivo `index.html` em `/Documentation`

**Coolify Docs:** https://coolify.io/docs

**Discord Coolify:** https://coollabs.io/discord

---

## 🎉 Vantagens do Deploy no Coolify

| Recurso                 | Manual           | Coolify             |
| ----------------------- | ---------------- | ------------------- |
| **Tempo de Setup**      | 2-4 horas        | 30 min              |
| **SSL**                 | Manual (certbot) | Automático          |
| **Proxy**               | Nginx manual     | Traefik auto        |
| **Updates**             | SSH + comandos   | Git push → Redeploy |
| **Rollback**            | Complexo         | 1 clique            |
| **Backup BD**           | Scripts cron     | Interface UI        |
| **Múltiplos Ambientes** | Difícil          | Simples             |
| **Monitoramento**       | Instalar tools   | Integrado           |

**Boa sorte com seu deploy! 🚀**
