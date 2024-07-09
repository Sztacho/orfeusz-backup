FROM debian:bullseye-slim

ENV DEBIAN_FRONTEND="noninteractive"
ENV TZ=Europe/Warsaw
ENV SITE_PATH=/var/www/html/

ARG PHP_VER=8.2

RUN set -ex; \
    AptInstall=" \
        cron \
        git \
        nginx \
        openssl \
        postfix \
        nano \
        netcat \
        unzip \
        vim \
        zip \
        openssh-client \
    "; \
    PHPInstall=" \
        php${PHP_VER} \
        php${PHP_VER}-apcu \
        php${PHP_VER}-bcmath \
        php${PHP_VER}-bz2 \
        php${PHP_VER}-cgi \
        php${PHP_VER}-cli \
        php${PHP_VER}-common \
        php${PHP_VER}-curl \
        php${PHP_VER}-dba \
        php${PHP_VER}-dom \
        php${PHP_VER}-enchant \
        php${PHP_VER}-fpm \
        php${PHP_VER}-gd \
        php${PHP_VER}-gmp \
        php${PHP_VER}-iconv \
        php${PHP_VER}-interbase \
        php${PHP_VER}-intl \
        php${PHP_VER}-mbstring \
        php${PHP_VER}-mcrypt \
        php${PHP_VER}-memcached \
        php${PHP_VER}-mysql \
        php${PHP_VER}-odbc \
        php${PHP_VER}-opcache \
        php${PHP_VER}-pdo \
        php${PHP_VER}-pgsql \
        php${PHP_VER}-phar \
        php${PHP_VER}-pspell \
        php${PHP_VER}-readline \
        php${PHP_VER}-redis \
        php${PHP_VER}-soap \
        php${PHP_VER}-sqlite3 \
        php${PHP_VER}-tidy \
        php${PHP_VER}-xml \
        php${PHP_VER}-xsl \
        php${PHP_VER}-zip \
    "; \
    BuildDeps=" \
        apt-transport-https \
        ca-certificates \
        curl \
        procps \
        supervisor \
        tzdata \
        wget \
    "; \
    TempBuildDeps=" \
        dirmngr \
        gnupg \
        software-properties-common \
    "; \
    apt update; \
    apt install --no-install-recommends -qy $BuildDeps $TempBuildDeps; \
    apt install mariadb-client --no-install-recommends -qy; \
    apt install nodejs -qy; \
    apt install unzip -qy; \
    wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -; \
    echo "deb https://packages.sury.org/php/ $(lsb_release -cs) main" >> /etc/apt/sources.list.d/php.list; \
    wget http://nginx.org/keys/nginx_signing.key; apt-key add nginx_signing.key; \
    echo "deb http://nginx.org/packages/debian/ $(lsb_release -cs) nginx" >> /etc/apt/sources.list.d/nginx.list; \
    apt update -q; apt install --no-install-recommends -qy $PHPInstall $AptInstall; \
    \
    mkdir -p /var/log/supervisor; mkdir /run/php; \
    ln -s /usr/sbin/php-fpm${PHP_VER} /usr/sbin/php-fpm; \
    cp /usr/share/zoneinfo/${TZ} /etc/localtime; echo "${TZ}" > /etc/timezone; \
    apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*; \
    apt-get purge -y --auto-remove $TempBuildDeps;

RUN wget https://getcomposer.org/download/latest-stable/composer.phar
RUN chmod 755 composer.phar
RUN mv composer.phar /usr/local/bin/composer

WORKDIR ${SITE_PATH}

#node
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash
RUN apt-get install -y lsb-release nodejs
RUN npm install -g yarn

COPY config/php.ini /etc/php/${PHP_VER}/fpm/conf.d/zzz-custom.ini
COPY config/php-fpm.conf /etc/php/${PHP_VER}/fpm/pool.d/zzz-custom.conf
COPY config/nginx-orfeusz.conf /etc/nginx/nginx-orfeusz.conf
COPY config/nginx-main.conf /etc/nginx/nginx.conf
COPY config/nginx-site.conf /etc/nginx/conf.d/default.conf
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN nginx -t
RUN echo "Host gitlab.com\n    StrictHostKeyChecking no" >> /etc/ssh/ssh_config

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

