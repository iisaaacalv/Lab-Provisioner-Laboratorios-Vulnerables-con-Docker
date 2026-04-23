#!/bin/bash

IMAGE_NAME="lab3-upload"
CONTAINER_NAME="lab3-upload-container"

echo "[*] Construyendo la imagen Docker..."
docker build -t $IMAGE_NAME ./

echo "[*] Eliminando contenedor anterior si existe..."
docker rm -f $CONTAINER_NAME 2>/dev/null

echo "[*] Lanzando el contenedor..."
docker run -d \
  --name $CONTAINER_NAME \
  -p 8888:80 \
  $IMAGE_NAME

echo ""
echo "[+] Lab 3 corriendo."
echo "[+] Acceder en: http://127.0.0.1:8888"
echo "[+] Objetivo: subir una webshell y leer /var/www/html/.flag"

# Opcional: lanzar el playbook de Ansible si está instalado
if command -v ansible-playbook &> /dev/null; then
    echo ""
    echo "[*] Ansible detectado. Ejecutando playbook de verificación..."
    sleep 3  # Esperamos a que el contenedor esté listo
    ansible-playbook -i ./ansible/inventory \
                        ./ansible/playbook.yml
else
    echo "[!] Ansible no detectado. Saltando playbook (no es obligatorio)."
fi
