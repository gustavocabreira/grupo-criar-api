FROM php:8.4-cli-alpine

ARG USER_ID
ARG GROUP_ID

RUN apk update && apk add --no-cache \
    git \
    curl \
    freetype-dev \
    libjpeg-turbo-dev \
    libmemcached-dev \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    librdkafka-dev \
    postgresql-dev \
    openssh \
    zip \
    unzip \
    supervisor \
    nano \
    dcron \
    nodejs \
    npm \
    sudo \
    autoconf \
    g++ \
    make \
    rsync \
    sqlite \
    sqlite-dev \
    linux-headers \
    gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl

RUN pecl install swoole && docker-php-ext-enable swoole
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pcntl mysqli pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name 'xdebug.so' | head -n 1)" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g ${GROUP_ID} laravel \
    && adduser -u ${USER_ID} -G laravel -s /bin/sh -D laravel \
    && echo "laravel:password1234" | chpasswd

RUN apk add --no-cache sudo \
    && echo 'laravel ALL=(ALL) NOPASSWD:ALL' > /etc/sudoers.d/laravel \
    && chmod 0440 /etc/sudoers.d/laravel

RUN echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

COPY ./supervisor.conf /etc/supervisor.conf
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./laravel-install.sh /usr/local/bin/laravel-install.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/laravel-install.sh

EXPOSE 9051
USER laravel
WORKDIR /var/www
ENTRYPOINT [ "/usr/local/bin/laravel-install.sh" ]
CMD ["supervisord", "-c", "/etc/supervisor.conf"]
