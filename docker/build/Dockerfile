ARG PHP_VERSION=8.4
ARG NGINX_VERSION=1.27.3
ARG NODE_VERSION=20
ARG ANGULAR_VERSION=19.0.6
ARG NPM_PINNED_VERSION=^11.3.0

# Base image
FROM php:${PHP_VERSION}-fpm-alpine AS app_php

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY=${STABILITY}

# Allow to select Symfony version
ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION=${SYMFONY_VERSION}

ENV APP_ENV=prod

WORKDIR /srv/app

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest --link /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
RUN sed -i -e 's/http:/https:/' /etc/apk/repositories
# hadolint ignore=DL3018
RUN apk add ca-certificates --no-cache
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

RUN set -eux; \
    install-php-extensions \
		apcu \
		intl \
		xml \
		opcache \
		zip \
    ;

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN apk add --no-cache --virtual .pgsql-deps postgresql-dev; \
	docker-php-ext-install -j$(nproc) pdo_pgsql; \
	apk add --no-cache --virtual .pgsql-rundeps so:libpq.so.5; \
	apk del .pgsql-deps
###< doctrine/doctrine-bundle ###
###< recipes ###

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/build/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link docker/build/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

COPY --link docker/build/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

COPY --link docker/build/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/build/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY --link api/composer.* api/symfony.* ./
RUN set -eux; \
    if [ -f composer.json ]; then \
		composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi

# copy sources
COPY --link  api ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer dump-env prod; \
		composer run-script --no-dev post-install-cmd; \
		chmod +x bin/console; sync; \
    fi

# Dev image
FROM app_php AS app_php_dev

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /srv/app/var/

RUN rm "$PHP_INI_DIR/conf.d/app.prod.ini"; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/build/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

#RUN set -eux; \
#	install-php-extensions \
#    	xdebug \
#    ;

RUN rm -f .env.local.php

# UI image
FROM node:${NODE_VERSION} AS build_ui

ARG APP_VERSION="dev"
ENV APP_VERSION ${APP_VERSION}

# install curl and jq
RUN apt-get update && apt-get install -y curl jq

# install chrome for protractor tests
#RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -
#RUN sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
#RUN apt-get update && apt-get install -yq google-chrome-stable bash

# set working directory
WORKDIR /app

# add `/app/node_modules/.bin` to $PATH
ENV PATH /app/node_modules/.bin:$PATH

# install and cache app dependencies
COPY ui/package.json /app/package.json
COPY ui/package-lock.json /app/package-lock.json
RUN npm install -g @angular/cli@${ANGULAR_VERSION}
RUN npm i -g npm@${NPM_PINNED_VERSION}
RUN npm ci

# add app
COPY ui /app

# replace version string in file
RUN sed -i "s/REPLACEME/${APP_VERSION}/g" /app/src/environments/environment.ts

# run tests
# RUN ng test --watch=false --browsers=ChromeHeadlessNoSandbox
# RUN ng e2e --port 4202

# generate build
RUN ng build --output-path=dist

############
### local-dev ###
############
FROM node:${NODE_VERSION} AS app_ui_local

WORKDIR /usr/src/app

COPY --from=build_ui /app /usr/src/app
RUN npm install -g @angular/cli@${ANGULAR_VERSION}
RUN npm i -g npm@${NPM_PINNED_VERSION}
RUN npm ci

EXPOSE 80

ENV PORT 80
ENV HOSTNAME "0.0.0.0"

CMD ["ng", "serve", "--host", "0.0.0.0", "--port", "80"]


FROM node:${NODE_VERSION} AS ui_local

RUN npm install -g @angular/cli@${ANGULAR_VERSION}

WORKDIR /
RUN mkdir app
WORKDIR /app

CMD ["ng", "serve", "--host", "0.0.0.0", "--port", "80"]

EXPOSE 80

###############
### prod ui ###
###############
FROM nginx:${NGINX_VERSION}-alpine AS app_ui

# copy artifact build from the 'build environment'
COPY --from=build_ui /app/dist/browser /usr/share/nginx/html

RUN chown nginx:nginx /usr/share/nginx/html/*

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]

################
### prod api ###
################
FROM nginx:${NGINX_VERSION}-alpine AS app_api

# copy artifact build from the 'build environment'
COPY --from=build_ui /app/dist /usr/share/nginx/html

RUN chown nginx:nginx /usr/share/nginx/html/*

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]

