FROM php:8.1-fpm
WORKDIR "/public_html"

# Install selected extensions and other stuff
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libxpm-dev \
    libfreetype6-dev \
    dos2unix \
    zsh

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Install NPM
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash
RUN apt-get install nodejs -y
RUN node -v && npm -v

# Set Paris timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql intl

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN set -eux; apt-get update; apt-get install -y libzip-dev zlib1g-dev; docker-php-ext-install zip

# Install Oh My Zsh
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
ADD https://pastebin.com/raw/C7utiTNE /root/.zshrc
RUN dos2unix /root/.zshrc

# Remove APT cache
RUN apt remove wget curl dos2unix -y
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo 'alias sf="php app/console"' >> ~/.bashrc
