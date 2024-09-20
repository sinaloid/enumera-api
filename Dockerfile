# Utiliser l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions PHP requises pour Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install zip pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le projet Laravel dans le conteneur
WORKDIR /var/www/html
COPY . /var/www/html

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Commande pour démarrer Apache
CMD ["apache2-foreground"]
