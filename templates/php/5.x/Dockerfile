FROM php:5.5-apache
LABEL authors="ebalo"

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Use the default production configuration
#RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install PHP extensions (only 5.5+)
RUN install-php-extensions zip

RUN service apache2 start