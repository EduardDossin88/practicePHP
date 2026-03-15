FROM php:8.5-fpm-alpine AS base
RUN apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql
WORKDIR /var/www/html

FROM base AS fpm
CMD ["php-fpm"]

FROM base AS cli
CMD ["php", "-a"]