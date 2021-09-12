FROM php:7.4-fpm

# Install modules
RUN apt-get update && apt-get install -y \
    libmcrypt-dev libxml2-dev libonig-dev libzip-dev libcurl4-openssl-dev libfreetype6-dev libjpeg62-turbo-dev libicu-dev wget git --no-install-recommends

RUN docker-php-ext-install curl zip mbstring xml pdo_mysql mysqli \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd \
    && pecl install mcrypt \
    && docker-php-ext-enable mcrypt

COPY --from=composer /usr/bin/composer /usr/bin/composer

ARG DEBUG_ENABLE=0

RUN if [ "$DEBUG_ENABLE" = 1 ] ; then \
    echo Enable xdebug && \
    pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear ; fi

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

RUN usermod -u 1000 www-data

WORKDIR /app

EXPOSE 9000

CMD ["php-fpm"]