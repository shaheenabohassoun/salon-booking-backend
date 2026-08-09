FROM php:8.3-cli-bookworm

RUN apt-get update \
  && apt-get install -y --no-install-recommends git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache \
  && chmod +x docker-entrypoint.sh \
  && php artisan package:discover --ansi || true

ENV PORT=8000
EXPOSE 8000

CMD ["./docker-entrypoint.sh"]
