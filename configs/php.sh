#!/bin/bash

set -e

env=${APP_ENV:-production}
role=${CONTAINER_ROLE:-fpm}

if [[ "$role" == "queue" ]]; then

    exec php /app/artisan queue:work --verbose --tries=1 --timeout=120

elif [[ "$role" == "cron" ]]; then

    exec crond -f

elif [[ "$role" == "fpm" ]]; then

    if [[ "$env" == "dev" ]]; then

        (composer install --no-interaction)

    else

        (
            composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev \
            && php artisan optimize
        )

    fi

    (
        php artisan storage:link \
        && chown -R www-data:www-data . \
        && chmod -R 755 storage \
        && chmod -R 755 bootstrap/ \
        && php artisan migrate --force
    )

    exec php-fpm

fi
