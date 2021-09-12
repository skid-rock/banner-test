FROM php:7.4-cli

RUN docker-php-ext-install pdo_mysql \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN docker-php-ext-configure pcntl --enable-pcntl \
      && docker-php-ext-install pcntl

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

CMD ["/app/bin/console.php", "consume"]