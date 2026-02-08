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
    git \
    netcat-openbsd \
    icu-dev \
    icu-data-full

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

# Configurar PHP
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "max_input_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "default_socket_timeout = 600" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "upload_tmp_dir = /tmp" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "file_uploads = On" >> /usr/local/etc/php/conf.d/uploads.ini

# Configurar PHP-FPM
RUN echo "request_terminate_timeout = 600" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "request_slowlog_timeout = 0" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_value[error_log] = /var/log/php-fpm-error.log" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf

# Configurar Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicialização
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Criar diretórios necessários
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx \
    && mkdir -p /tmp \
    && chmod 1777 /tmp \
    && chown root:root /tmp

# Expor porta 80
EXPOSE 80

# Comando de inicialização
CMD ["/start.sh"]
