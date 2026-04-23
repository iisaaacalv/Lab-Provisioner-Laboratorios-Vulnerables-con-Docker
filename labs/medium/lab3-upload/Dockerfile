# Base Ubuntu 22.04
FROM ubuntu:22.04

# Sin prompts interactivos
ENV DEBIAN_FRONTEND=noninteractive

# Instalamos Apache y PHP.
# - apache2: el servidor web
# - php y libapache2-mod-php: para que Apache entienda archivos .php
# - php-cli: utilidades PHP desde línea de comandos
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-cli

# Copiamos los archivos de la aplicación web
# al directorio raíz de Apache (/var/www/html)
COPY files/index.php /var/www/html/index.php
COPY files/upload.php /var/www/html/upload.php

# Creamos el directorio donde se guardarán los archivos subidos.
# Le damos permisos completos para que Apache (www-data) pueda escribir ahí.
RUN mkdir -p /var/www/html/uploads
RUN chmod 777 /var/www/html/uploads

# Copiamos la flag y la ocultamos.
# El punto al inicio del nombre la hace "oculta" en Linux (ls normal no la muestra).
# La ponemos fuera del directorio web para que no sea accesible directamente.
RUN mkdir /susdir
COPY files/flag.txt /susdir/.flag
RUN chmod 644 /susdir/.flag

# Borramos el index.html por defecto de Apache
# para que nuestra aplicación sea lo primero que se vea.
RUN rm -f /var/www/html/index.html

# Habilitamos el módulo de reescritura de Apache (por si hace falta)
RUN a2enmod rewrite

# Exponemos el puerto 80 (HTTP estándar)
EXPOSE 80

# Arrancamos Apache en primer plano.
# "-D FOREGROUND" es el equivalente al "-D" de SSH: mantiene el proceso activo.
CMD ["apache2ctl", "-D", "FOREGROUND"]
