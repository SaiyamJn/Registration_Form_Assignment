FROM php:8.2-apache

# Install PDO MySQL
RUN apt-get update && apt-get install -y libzip-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql

# Copy app files into web root
COPY . /var/www/html/

# Ensure start script is executable
RUN chmod +x /var/www/html/start.sh

EXPOSE 80

CMD ["/var/www/html/start.sh"]
