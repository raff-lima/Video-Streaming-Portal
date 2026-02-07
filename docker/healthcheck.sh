#!/bin/bash
# Healthcheck para Coolify

# Verifica se o servidor web está respondendo
curl -f http://localhost/ || exit 1

# Verifica se o PHP-FPM está rodando
pidof php-fpm > /dev/null || exit 1

# Verifica se o Nginx está rodando
pidof nginx > /dev/null || exit 1

exit 0
