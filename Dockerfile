FROM composer:lts AS builder

WORKDIR /var/www/backend

COPY composer.lock composer.json /var/www/backend/

RUN composer install --no-dev --no-scripts

FROM php:8.3-fpm-alpine AS runner

WORKDIR /var/www/backend

COPY --from=builder /var/www/backend/vendor /var/www/backend/vendor

# Install dependencies and clean up in the same layer
RUN apk add --no-cache \
    libpng-dev \
    libzip-dev \
    postgresql-dev && \
    docker-php-ext-install pdo pdo_pgsql zip && \
    rm -rf /var/cache/apk/* && \
    addgroup -g 1000 www && \
    adduser -u 1000 -S www -G www && \
    chown -R www:www /var/www


# Copy application files
COPY --chown=www:www . /var/www/backend

USER www

EXPOSE 9000
CMD ["php-fpm"]
