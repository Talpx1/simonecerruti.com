# 1. BASE
FROM php:8.4-apache AS base
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    nano supervisor cron libpq-dev libicu-dev libzip-dev unzip bash logrotate postgresql-client && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN docker-php-ext-install pdo_pgsql opcache intl zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# 2. VENDOR
FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-progress --no-scripts --no-dev --optimize-autoloader

# 3. ASSETS
FROM node:24 AS assets
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts --no-audit --no-fund
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
RUN npm run build

# 4. ACTUAL
FROM base AS web

ARG APP_ENV="production"
ARG WWWUID=1000
ARG WWWGID=1000

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite
# Configure Apache DocumentRoot to point to Laravel's public directory
# and update Apache configuration files
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Add Apache config to log to Laravel's storage/logs
RUN echo 'ErrorLog /var/www/html/storage/logs/apache_error.log' > /etc/apache2/conf-available/custom-logs.conf && \
    echo 'CustomLog /var/www/html/storage/logs/apache_access.log combined' >> /etc/apache2/conf-available/custom-logs.conf && \
    a2enconf custom-logs

# Setup supervisord configs
COPY ./docker/configs/supervisord.conf /etc/supervisor/supervisord.conf

# Setup cron supervisor (for Laravel scheduler)
COPY ./docker/configs/crontab /etc/crontab
COPY ./docker/configs/laravel-cron-supervisor.conf /etc/supervisor/conf.d/laravel-cron.conf

# Setup worker supervisor (for Laravel queues)
COPY ./docker/configs/laravel-worker-supervisor.conf /etc/supervisor/conf.d/laravel-worker.conf

# Setup vite watch supervisor (dev)
COPY ./docker/configs/vite-watch.conf /etc/supervisor/conf.d/vite-watch.conf

# Setup apache supervisor
COPY ./docker/configs/apache-supervisor.conf /etc/supervisor/conf.d/apache.conf

# Setup logrotate
COPY ./docker/configs/logrotate /etc/logrotate.d/simone_cerruti_website

# Setup Watchtower lifecycle executables
COPY ./docker/scripts/post-update.sh /post-update.sh
RUN chmod +x /post-update.sh

# Setup healthcheck script
COPY ./docker/scripts/healthcheck.sh /healthcheck.sh
RUN chmod +x /healthcheck.sh

# Setup opcache
COPY ./docker/configs/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

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
    echo "PS1='\\[\\033[01;32m\\]\\u@\\h\\[\\033[00m\\]:\\[\\033[01;34m\\]\\w\\[\\033[00m\\]\\$ '" >> /etc/bash.bashrc


# Run setup for local development if app env is local
RUN if [ "$APP_ENV" = "local" ] ; then \
    apt-get update && \
    apt-get install -y --no-install-recommends git ssh && \
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

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf", "-n"]
