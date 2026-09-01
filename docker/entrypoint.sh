#!/bin/sh
set -e

APP_ROOT=/var/www/html
cd "$APP_ROOT"

# When the project directory is bind-mounted from the host, it shadows the
# vendor/ baked into the image. Restore it so the container is usable either way.
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/autoload.php missing — running composer install"
    composer install --no-interaction --no-progress --no-scripts --prefer-dist
fi

# config/app_local.php is gitignored, so it may not exist. Fall back to the
# env-driven container config.
if [ ! -f config/app_local.php ]; then
    echo "[entrypoint] config/app_local.php missing — using env-driven container config"
    cp docker/app_local.php config/app_local.php
fi

if [ -z "${SECURITY_SALT}" ]; then
    echo "[entrypoint] WARNING: SECURITY_SALT is not set. Set it in docker-compose.yml."
    echo "[entrypoint]          Generate one with: openssl rand -hex 32"
fi

# CakePHP needs these writable at runtime; they are gitignored so a fresh
# checkout will not contain them.
mkdir -p tmp/cache/models tmp/cache/persistent tmp/cache/views tmp/sessions tmp/tests logs data
chown -R www-data:www-data tmp logs data 2>/dev/null || true

exec "$@"
