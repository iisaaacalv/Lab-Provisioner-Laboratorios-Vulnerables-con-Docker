#!/bin/bash

IMAGE_NAME="lab2-suid"
CONTAINER_NAME="lab2-suid-container"

echo "[*] Construyendo la imagen Docker..."
docker build -t $IMAGE_NAME ./

echo "[*] Eliminando contenedor anterior si existe..."
docker rm -f $CONTAINER_NAME 2>/dev/null

echo "[*] Lanzando el contenedor..."
docker run -d \
  --name $CONTAINER_NAME \
  -p 2222:22 \
  $IMAGE_NAME

echo ""
echo "[+] Lab 2 corriendo."
echo "[+] Conectar con: ssh -p 2222 lowpriv@127.0.0.1"
echo "[+] Contraseña: user1234"
echo "[+] Objetivo: escalar a root y encontrar la flag"
