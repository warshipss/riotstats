FROM php:7.4-fpm-alpine

WORKDIR /app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && chmod +x /usr/local/bin/composer \
    && apk --updated --no-cache add bash autoconf g++ make \
    && docker-php-ext-install pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./configs/php.ini $PHP_INI_DIR/conf.d/

COPY ./configs/php.sh /usr/local/bin/
COPY ./configs/crontab /etc/crontab

RUN chmod +x /usr/local/bin/php.sh \
    && crontab /etc/crontab

ENTRYPOINT ["php.sh"]
