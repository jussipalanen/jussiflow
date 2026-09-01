# syntax=docker/dockerfile:1

###############################################################################
# Stage 1 — Base platform
#
# PHP + the extensions CakePHP needs. Shared by the dependency and runtime
# stages so that Composer resolves the lock file against exactly the platform
# the application will run on (ext-intl in particular — resolving on a stock
# composer image fails, because that image ships neither intl nor PHP 8.5).
###############################################################################
FROM php:8.5-apache AS base

# CakePHP requires ext-intl. zip speeds up Composer. mbstring, pdo_sqlite, dom
# and xml ship with the base image, and opcache is compiled into php:8.5 and
# enabled by default — building it here fails, since it produces no .so to
# install.
#
# libicu-dev is intentionally NOT purged afterwards: the compiled intl extension
# links against the ICU runtime libraries it provides.
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###############################################################################
# Stage 2 — Composer dependencies
#
# Isolated so that dependency installation is cached and only re-runs when
# composer.json / composer.lock actually change.
###############################################################################
FROM base AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# --no-scripts: composer.json's post-install-cmd calls App\Console\Installer,
# which needs src/ and an interactive terminal. The entrypoint handles setup.
RUN composer install \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --prefer-dist

###############################################################################
# Stage 3 — Application runtime
###############################################################################
FROM base

# Match the container's web user to the host user so that bind-mounted files
# (owned by the host UID) stay writable from inside the container. Override at
# build time with --build-arg UID=$(id -u) if your host user is not 1000.
ARG UID=1000
ARG GID=1000

# CakePHP relies on mod_rewrite for its .htaccess routing rules.
RUN a2enmod rewrite

COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Align the www-data user with the host UID/GID (see ARG comment above).
RUN groupmod -o -g "${GID}" www-data \
    && usermod  -o -u "${UID}" -g "${GID}" www-data

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor
COPY . .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

RUN mkdir -p tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions tmp/tests logs data \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
