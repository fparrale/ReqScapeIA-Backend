# Usar una imagen base de PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Copiar el código de la API al directorio del servidor web
COPY . /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Habilitar mod_rewrite para permitir URLs amigables
RUN a2enmod rewrite

# Cambiar permisos de la carpeta para que Apache pueda acceder
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80 para acceder al servidor
EXPOSE 80

# Crear un volumen para mantener el código sincronizado
VOLUME ["/var/www/html"]
