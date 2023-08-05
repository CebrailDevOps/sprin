FROM php:8.2-apache

# Install mysqli
RUN apt-get update && \
    apt-get install -y inetutils-ping && \
    docker-php-ext-install pdo_mysql

# Install ssh client
RUN apt-get update && apt-get install -y openssh-client

# Install sudo
RUN apt-get update && apt-get install -y sudo

# Allow www-data to use sudo without password
RUN echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers.d/www-data
RUN chmod 0440 /etc/sudoers.d/www-data

# Enable apache rewrite module
RUN a2enmod rewrite