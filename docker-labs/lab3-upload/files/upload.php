<?php
// Comprobamos que se haya subido algo
if (isset($_FILES['archivo'])) {

    $nombre    = $_FILES['archivo']['name'];       // nombre original del archivo
    $tmp_path  = $_FILES['archivo']['tmp_name'];   // ruta temporal donde PHP lo guarda
    $destino   = "uploads/" . $nombre;             // dónde lo vamos a mover definitivamente

    // -------------------------------------------------------
    // VALIDACIÓN INCORRECTA (vulnerabilidad intencionada)
    // Solo comprueba si el nombre contiene ".jpg", ".png" etc.
    // Pero NO valida el contenido real del archivo (MIME type).
    // Un atacante puede subir "shell.php" y esto no lo bloquea.
    // -------------------------------------------------------
    $extensiones_permitidas = array(".jpg", ".jpeg", ".png", ".pdf", ".doc");
    $es_valido = false;

    foreach ($extensiones_permitidas as $ext) {
        // strpos comprueba si la extensión aparece en algún lugar del nombre
        // Problema: "shell.php" no contiene ".jpg", así que pasa igual
        // Y si alguien pone "imagen.jpg.php" tampoco lo detecta bien
        if (strpos($nombre, $ext) !== false) {
            $es_valido = true;
        }
    }

    // Si el archivo tiene una extensión "válida" O si la validación falla,
    // lo movemos igualmente. En realidad, cualquier .php pasa sin problemas.
    if (move_uploaded_file($tmp_path, $destino)) {
        echo "<h3 style='color:green; font-family:Arial;'>
              ✅ Archivo subido correctamente: <a href='$destino'>$nombre</a>
              </h3>";
        echo "<p style='font-family:Arial;'>
              <a href='/'>← Volver</a>
              </p>";
    } else {
        echo "<h3 style='color:red; font-family:Arial;'>
              ❌ Error al subir el archivo.
              </h3>";
    }
} else {
    // Si alguien accede a upload.php directamente sin subir nada
    header("Location: /");
}
?>
