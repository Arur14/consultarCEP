# Use a imagem oficial do PHP com FPM
FROM php:8.1-fpm

# Instale as dependências necessárias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

# Copie os arquivos do projeto Symfony para o contêiner
COPY ./src /var/www/html

# Copie o arquivo composer.json e composer.lock para instalar as dependências posteriormente
COPY composer.json composer.lock /var/www/html/

# Instala as dependências do Symfony (Composer)
# RUN composer install --no-scripts --no-autoloader --prefer-dist --no-progress --no-interaction

# Gere o autoload
RUN composer dump-autoload --optimize

# Ajuste permissões para garantir que o diretório de cache e logs sejam acessíveis
# RUN chown -R www-data:www-data /var/www/html/var/cache /var/www/html/var/log

# Exponha a porta 9000 para o PHP-FPM
EXPOSE 9000

# Inicia o PHP-FPM no contêiner
CMD ["php-fpm"]