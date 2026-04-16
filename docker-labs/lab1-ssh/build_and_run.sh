#!/bin/bash

# Nombre del contenedor e imagen
IMAGE_NAME="lab1-ssh"
CONTAINER_NAME="lab1-ssh-container"

echo "[*] Eliminando contenedor anterior si existe..."
docker rm -f $CONTAINER_NAME 2>/dev/null

echo "[*] Construyendo la imagen Docker..."
docker build -t $IMAGE_NAME ./

echo "[*] Lanzando el contenedor..."
docker run -d \
  --name $CONTAINER_NAME \
  -p 45768:45768 \
  -p 8080:8080 \
  -p 8443:8443 \
  -p 9090:9090 \
  -p 3306:3306 \
  -p 6379:6379 \
  -p 27017:27017 \
  $IMAGE_NAME
