FROM php:8.3-fpm-bullseye

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zlib1g-dev \
    && docker-php-ext-install \
        intl \
        zip \
        opcache \
        pdo \
        pdo_mysql \
        pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN addgroup --gid 1000 dev && \
    adduser --uid 1000 --gid 1000 --disabled-password --gecos "" dev

RUN echo 'memory_limit = 2048M' > /usr/local/etc/php/conf.d/memory-limit.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER dev
WORKDIR /var/www/html/app

EXPOSE 9000