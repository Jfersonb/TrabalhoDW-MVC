FROM php:8.4-cli

# Instala git, unzip e Composer
RUN apt-get update && apt-get install -y git unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala a extensão sockets (usada pelo php-amqplib)
RUN docker-php-ext-install sockets

# Define o diretório de trabalho
WORKDIR /app

# Comando para instalar dependências e iniciar o serviço
CMD ["bash", "-c", "composer install && php /app/scripts/mail.php"]