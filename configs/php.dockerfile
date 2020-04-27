FROM php:7.4-fpm-alpine

WORKDIR /app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && chmod +x /usr/local/bin/composer \
    && apk --update --no-cache add bash autoconf postgresql-dev g++ make \
    && docker-php-ext-install pcntl pdo pdo_pgsql pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY ./configs/php.ini-dev /etc
COPY ./configs/php.ini-prod /etc

COPY ./configs/php.sh /usr/local/bin/
COPY ./configs/crontab /etc/crontab

RUN chmod +x /usr/local/bin/php.sh \
    && crontab /etc/crontab

ENTRYPOINT ["php.sh"]
