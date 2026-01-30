FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip sockets \
    && rm -rf /var/lib/apt/lists/*

# Installation de composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/php

# Pour laisser le conteneur tourner indéfiniment (en attendant le script de consommation)
CMD ["tail", "-f", "/dev/null"]
