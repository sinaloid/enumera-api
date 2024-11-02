# Utiliser l'image officielle de PHP 8.1 avec Apache
FROM php:8.1-apache

# Installer les dépendances système et extensions PHP requises
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Installer Composer globalement
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le contenu du projet dans le répertoire /var/www
WORKDIR /var/www
COPY . /var/www

# Installer les dépendances PHP de Laravel
RUN composer install --optimize-autoloader --no-dev

# Configurer les permissions pour Laravel (pour Apache)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Changer le DocumentRoot d'Apache pour le dossier public de Laravel
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Activer le module mod_rewrite d'Apache pour supporter les URLs propres de Laravel
RUN a2enmod rewrite

# Exposer le port 80 pour Apache
EXPOSE 80

# Lancer Apache en mode foreground (le service principal)
CMD ["apache2-foreground"]
