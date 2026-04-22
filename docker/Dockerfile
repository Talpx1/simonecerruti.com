# ========================
# 1. BASE
# ========================
FROM php:8.5-fpm AS base

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    nano \
    nginx \
    supervisor \
    cron \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    bash \
    logrotate \
    gettext-base \
    postgresql-client \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    webp && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_pgsql intl zip exif gd bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ========================
# 2. VENDOR
# ========================
FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-progress --no-scripts --no-dev --optimize-autoloader

# ========================
# 3. ASSETS
# ========================
FROM node:24 AS assets

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts --no-audit --no-fund

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor

RUN npm run build

# ========================
# 4. ACTUAL
# ========================
FROM base AS web

ARG APP_ENV="production"
ARG WWWUID=1000
ARG WWWGID=1000

# Nginx config
COPY ./docker/configs/nginx.conf /etc/nginx/sites-available/default

# Setup PHP-FPM pool config
COPY ./docker/configs/php-fpm-www.conf /usr/local/etc/php-fpm.d/www.conf

# Setup php.ini
COPY ./docker/configs/php.ini $PHP_INI_DIR/php.ini

# Setup supervisord configs
COPY ./docker/configs/supervisord.conf /etc/supervisor/supervisord.conf

# Setup cron supervisor (for Laravel scheduler)
COPY ./docker/configs/crontab /etc/crontab
RUN chmod 0644 /etc/crontab && crontab /etc/crontab
COPY ./docker/configs/laravel-cron-supervisor.conf /etc/supervisor/conf.d/laravel-cron.conf

# Setup worker supervisor (for Laravel queues)
COPY ./docker/configs/laravel-worker-supervisor.conf /etc/supervisor/conf.d/laravel-worker.conf.template

# Setup vite watch supervisor (dev)
COPY ./docker/configs/vite-watch.conf /etc/supervisor/conf.d/vite-watch.conf

# Setup nginx supervisor
COPY ./docker/configs/nginx-supervisor.conf /etc/supervisor/conf.d/nginx.conf

# Setup php-fpm supervisor
COPY ./docker/configs/php-fpm-supervisor.conf /etc/supervisor/conf.d/php-fpm.conf

# Setup logrotate
COPY ./docker/configs/logrotate /etc/logrotate.d/simone_cerruti_website

# Setup Watchtower lifecycle executables
COPY ./docker/scripts/post-update.sh /post-update.sh
RUN chmod +x /post-update.sh

# Setup healthcheck script
COPY ./docker/scripts/healthcheck.sh /healthcheck.sh
RUN chmod +x /healthcheck.sh

# Setup opcache
COPY ./docker/configs/opcache.ini /usr/local/etc/php/conf.d/opcache.ini.template

# Setup entrypoint
COPY ./docker/scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set the working directory
WORKDIR /var/www/html

# preparing cache folders so its sure they exists and that they will be owned by www-data
RUN mkdir -p /var/www/.cache /var/www/.npm /var/www/.config/psysh && \
    chown -R www-data:www-data /var/www/.cache /var/www/.npm /var/www/.config/psysh

COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /var/www/html/public/build ./public/build
# Copy app code
COPY . .
# Remove the docker folder as its not needed in non-deploy envs
RUN rm -rf ./docker

# bash as default with colors and aliases
RUN usermod -s /bin/bash www-data && \
    echo "export TERM=xterm-256color" >> /etc/bash.bashrc && \
    echo "alias ls='ls --color=auto'" >> /etc/bash.bashrc && \
    echo "alias grep='grep --color=auto'" >> /etc/bash.bashrc && \
    echo "alias fgrep='fgrep --color=auto'" >> /etc/bash.bashrc && \
    echo "alias egrep='egrep --color=auto'" >> /etc/bash.bashrc && \
    echo "alias artisan='php artisan'" >> /etc/bash.bashrc && \
    echo "alias tinker='php artisan tinker'" >> /etc/bash.bashrc && \
    echo "PS1='\\[\\033[01;32m\\]\\u@\\h\\[\\033[00m\\]:\\[\\033[01;34m\\]\\w\\[\\033[00m\\]\\$ '" >> /etc/bash.bashrc


# Run setup for local development if app env is local
RUN if [ "$APP_ENV" = "local" ] ; then \
    apt-get update && \
    apt-get install -y --no-install-recommends git ssh xclip && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    docker-php-ext-install pcntl && \
    # install node
    curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest && \
    # change gid
    if [ "$(id -g www-data)" != "$WWWGID" ]; then \
    groupmod -o -g "$WWWGID" www-data ; \
    fi && \
    # change uid and shell to bash
    if [ "$(id -u www-data)" != "$WWWUID" ]; then \
    usermod -o -u "$WWWUID" www-data ; \
    fi \
    else \
    rm /etc/supervisor/conf.d/vite-watch.conf; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www

ENTRYPOINT ["/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf", "-n"]
