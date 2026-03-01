FROM php:8.5-cli

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/

RUN apt-get update && apt-get install -y --no-install-recommends \
  git \
  unzip \
  && rm -rf /var/lib/apt/lists/*
