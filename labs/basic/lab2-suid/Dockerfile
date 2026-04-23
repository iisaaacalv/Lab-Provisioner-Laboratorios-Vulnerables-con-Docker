# Base Ubuntu 22.04, igual que el Lab 1.
# Consistencia entre labs facilita el mantenimiento.
FROM ubuntu:22.04

# Desactivamos prompts interactivos de apt.
ENV DEBIAN_FRONTEND=noninteractive

# Instalamos openssh-server (para que el alumno entre por SSH)
# y find (que será el binario vulnerable con SUID).
# find ya viene instalado normalmente, pero lo ponemos explícito.
RUN apt-get update && apt-get install -y openssh-server findutils

# Creamos el usuario de entrada: "lowpriv"
# Es el usuario sin privilegios desde donde el alumno empieza.
RUN useradd -m -s /bin/bash lowpriv
RUN echo "lowpriv:user1234" | chpasswd

# Creamos el directorio de SSH
RUN mkdir -p /var/run/sshd

# Configuramos SSH básico:
# - Puerto estándar 22 (en este lab el reto NO es encontrar el SSH,
#   sino escalar privilegios una vez dentro)
# - Autenticación por contraseña habilitada
RUN echo "PasswordAuthentication yes" >> /etc/ssh/sshd_config
RUN echo "PermitRootLogin no" >> /etc/ssh/sshd_config

#Creamos los directorios
RUN mkdir -p /root/nothing_here/go_deeper/flags/finale/almost_there/final_push/the_end

#Creamos archivo de pista
RUN echo "are you sure you looked DEEP enough?" > /root/nothing_here/go_deeper/flags/finale/almost_there/final_push/the_end/nottheflag.txt

# Copiamos la flag.
# Solo root puede leerla (permisos 600 = solo el propietario puede leer/escribir).
COPY files/flag.txt /root/nothing_here/go_deeper/.flag.txt
RUN chmod 600 /root/nothing_here/go_deeper/.flag.txt

# ============================================================
# CONFIGURACIÓN DE BINARIOS SUID
# Aquí está el corazón del lab.
# ============================================================

# Binarios SUID LEGÍTIMOS (están ahí para hacer ruido y confundir):
# Estos existen en sistemas reales y normalmente no son explotables
# (o son más difíciles de explotar sin contexto adicional).
# Los añadimos para que el alumno tenga que investigar cada uno.
RUN chmod u+s /usr/bin/passwd
RUN chmod u+s /usr/bin/newgrp
RUN chmod u+s /usr/bin/su
RUN chmod u+s /usr/bin/chsh
RUN chmod u+s /usr/bin/chfn

# Binario SUID VULNERABLE: find
# find tiene una funcionalidad (-exec) que permite ejecutar comandos.
# Si tiene SUID de root, esos comandos se ejecutan como root.
# Está documentado en GTFOBins: https://gtfobins.github.io/gtfobins/find/
RUN chmod u+s /usr/bin/find

# Creamos un archivo señuelo en /home/lowpriv para dar contexto
# y simular que hay "algo que investigar" en el sistema.
RUN echo "Sistema de monitoreo interno v1.2 - Solo administradores" \
    > /home/lowpriv/README.txt

# Arrancamos el servidor SSH en primer plano
CMD ["/usr/sbin/sshd", "-D"]
