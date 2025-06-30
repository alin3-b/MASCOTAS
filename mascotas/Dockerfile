FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite si usas URLs limpias
RUN a2enmod rewrite

# Permisos
RUN chown -R www-data:www-data /var/www/html

# Agregar configuración de acceso
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf && \
    a2enconf app

