FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

CMD ["bash"]
