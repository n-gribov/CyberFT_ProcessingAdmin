FROM debian:buster

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -y wget curl sudo nano make libpq-dev apt-utils zip git libaio1 ca-certificates gnupg lsb-release \
    && apt-get clean && apt-get autoclean

RUN sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
RUN sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list'
RUN curl https://packages.sury.org/php/apt.gpg | sudo apt-key add -
RUN curl https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -

RUN apt-get update \
    && apt-get install -y php8.0-readline php8.0-fpm php8.0-common php8.0-cli \
        php8.0-gd php8.0-curl php8.0-intl php8.0-mbstring \
        php8.0-zip php8.0-xml php8.0-pgsql \
        postgresql-client-12 nginx redis-server \
    && apt-get clean && apt-get autoclean

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet \
    && mv composer.phar /usr/local/bin/composer

RUN rm -rf /etc/nginx/sites-enabled/default

RUN echo 'PGSERVICEFILE="/etc/pgsysconfdir/pg_service.conf"' >> /etc/environment

EXPOSE 80
EXPOSE 443
ENV PGSERVICEFILE=/etc/pgsysconfdir/pg_service.conf
WORKDIR /var/www/app
CMD /bin/bash
LABEL cyberft-processing='v3'
