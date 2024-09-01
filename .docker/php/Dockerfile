FROM php:8.3.11-alpine

ARG USER_UID=1000
ARG GROUP_GID=1000
ARG UNAME=app
ARG UGNAME=app

RUN addgroup -g ${GROUP_GID} -S ${UGNAME}
RUN adduser --system --disabled-password --home /home/${UNAME} \
    --uid ${USER_UID} --ingroup ${UGNAME} ${UNAME}

RUN apk --no-cache add bash \
    build-base gcc wget zip unzip \
    git autoconf g++ make linux-headers \
    libtool oniguruma-dev sqlite

RUN docker-php-ext-install mbstring pdo pdo_mysql

RUN pecl install xdebug-3.3.2 \
	&& docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p /var/www/html
RUN chown -R ${USER_UID}:${USER_UID} /var/www/html

USER ${USER_UID}
