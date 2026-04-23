# Usamos Ubuntu 22.04 como base.
# Es estable, conocida y tiene los paquetes que necesitamos.
FROM ubuntu:22.04

# Evitamos que apt nos haga preguntas interactivas durante la instalación.
# Sin esto, algunos paquetes se quedan esperando input y el build falla.
ENV DEBIAN_FRONTEND=noninteractive

# Actualizamos la lista de paquetes e instalamos lo necesario:
# - openssh-server: el servidor SSH
# - passwd: para poder crear usuarios y cambiar contraseñas
RUN apt-get update && apt-get install -y openssh-server passwd

# Creamos el usuario "jdoe" (John Doe), que es el objetivo del alumno.
# -m crea su carpeta home (/home/jdoe)
# -s asigna bash como shell (para que pueda trabajar cómodamente)
RUN useradd -m -s /bin/bash jdoe

# Le asignamos la contraseña "shadow1" al usuario jdoe.
# El formato es "usuario:contraseña" | chpasswd
# "shadow1" está en rockyou.txt pero no es lo primero que prueba alguien,
# lo que lo hace realista para nivel principiante.
RUN echo "jdoe:shadow1" | chpasswd

# Creamos el directorio necesario para que el servidor SSH funcione.
# SSH necesita este directorio para almacenar datos de sesión.
RUN mkdir -p /var/run/sshd

# Copiamos nuestra configuración personalizada de SSH
# al lugar donde SSH la espera encontrar.
COPY config/sshd_config /etc/ssh/sshd_config

# Copiamos la flag al directorio /tmp y le damos permisos de lectura
# para que cualquier usuario (incluido jdoe) pueda leerla.
COPY files/flag.txt /tmp/flag.txt
RUN chmod 644 /tmp/flag.txt

# Copiamos nuestro banner SSH
COPY files/banner.txt /etc/ssh/banner.txt

# Exponemos el puerto 45768 (donde pondremos el SSH)
# y varios puertos de "ruido" para hacer el escaneo más realista.
EXPOSE 45768 8080 8443 9090 3306 6379 27017

# Cuando el contenedor arranca, lanzamos el servidor SSH en primer plano.
# "-D" significa "no te vayas al background", que es lo que necesita Docker.
CMD ["/usr/sbin/sshd", "-D"]
