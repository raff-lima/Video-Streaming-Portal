FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências primeiro (cache layer)
COPY composer.json composer.lock ./

# Instalar dependências do PHP
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar todo o código do projeto
COPY . .

# Finalizar instalação do Composer
RUN composer dump-autoload --optimize

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public

# Copiar configuração do Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copiar configuração do Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar script de inicialização
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Criar link simbólico do storage (será executado no start.sh se necessário)
# RUN php artisan storage:link

# Expor porta 80
EXPOSE 80

# Comando de inicialização
CMD ["/usr/local/bin/start.sh"]
