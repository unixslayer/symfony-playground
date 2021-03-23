FROM unixslayer/php:7.4-fpm

LABEL maintainer="Piotr Zając <piotr.zajac@unixslayer.pl>"

RUN apt update && apt install -y --no-install-recommends \
  libpq-dev \
  librabbitmq-dev \
  postgresql-client

RUN pecl install amqp-1.9.4

RUN docker-php-ext-enable amqp
RUN docker-php-ext-install -j$(nproc) pgsql pdo_pgsql sockets
