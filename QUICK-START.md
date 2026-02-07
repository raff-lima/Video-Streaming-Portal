# 🚀 GUIA RÁPIDO - 5 PASSOS PARA DEPLOY

## 1️⃣ SUBIR PARA O GIT (5 min)

```bash
cd "c:\Users\reury\Downloads\videoportal-24nulled\codecanyon-25581885-video-streaming-portal-tv-shows-movies-sports-videos-streaming\FOR FIRST TIME BUYER\video_streaming_portal"

git init
git add .
git commit -m "Projeto pronto para Coolify"
git branch -M main
git remote add origin https://github.com/SEU_USER/SEU_REPO.git
git push -u origin main
```

---

## 2️⃣ CRIAR MYSQL NO COOLIFY (2 min)

1. Abra seu Coolify
2. Vá em **Databases** → **Add New**
3. Escolha **MySQL 8.0**
4. Nome: `video_streaming_db`
5. Clique em **Create**
6. 📋 **ANOTE as credenciais** que aparecerem!

---

## 3️⃣ CRIAR APLICAÇÃO NO COOLIFY (3 min)

1. Vá em **Applications** → **Add New**
2. Conecte seu **repositório Git**
3. Branch: `main`
4. O Coolify vai detectar o **Dockerfile** automaticamente ✅
5. Clique em **Create**

---

## 4️⃣ CONFIGURAR VARIÁVEIS (3 min)

Vá em **Environment** e adicione (copie de `.env.production`):

### ⚠️ OBRIGATÓRIAS:

```bash
APP_NAME=Video Streaming Portal
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=video_streaming_db
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_mysql
DB_PASSWORD=senha_mysql

RUN_MIGRATIONS=true
```

📋 Use as credenciais que você anotou no passo 2!

---

## 5️⃣ DEPLOY! (5-10 min)

1. Clique em **Deploy**
2. Aguarde o build (5-10 min, acompanhe os logs)
3. Após concluir, abra o **Terminal** do container
4. Execute:

```bash
php artisan key:generate --show
```

5. Copie a chave gerada (começa com `base64:`)
6. Adicione em **Environment** → **APP_KEY**
7. Clique em **Restart**

---

## ✅ PRONTO!

Acesse: `https://seudominio.com/admin`

**Login:**

- Email: `admin@admin.com`
- Senha: `admin`

⚠️ **MUDE A SENHA IMEDIATAMENTE!**

---

## 🔧 Se algo der errado:

### Erro 500?

```bash
# No terminal do container:
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

### Não conecta no banco?

- Verifique se as credenciais em `DB_*` estão corretas
- Verifique se o MySQL está rodando no Coolify

### Upload não funciona?

```bash
php artisan storage:link
chmod -R 775 storage
```

---

## 📚 Mais Detalhes:

- **Guia Completo:** [DEPLOY_COOLIFY.md](DEPLOY_COOLIFY.md)
- **Checklist:** [CHECKLIST.md](CHECKLIST.md)
- **Leia-me:** [LEIA-ME.txt](LEIA-ME.txt)

---

**Tempo total: ~20 minutos** ⚡

Boa sorte! 🎉
