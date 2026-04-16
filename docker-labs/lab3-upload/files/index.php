<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal de Documentos - IntranetCorp</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: #eee; 
               display: flex; justify-content: center; padding-top: 80px; }
        .box { background: #16213e; padding: 40px; border-radius: 8px; 
               width: 420px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        h2 { color: #0f3460; color: #e94560; margin-bottom: 5px; }
        p.sub { color: #aaa; font-size: 13px; margin-bottom: 25px; }
        input[type=file] { width: 100%; padding: 10px; margin: 10px 0 20px; 
                           background: #0f3460; border: none; color: #eee; 
                           border-radius: 4px; }
        input[type=submit] { background: #e94560; color: white; border: none; 
                              padding: 12px 30px; cursor: pointer; 
                              border-radius: 4px; font-size: 15px; }
        input[type=submit]:hover { background: #c73652; }
        .notice { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
<div class="box">
    <h2>📁 Portal de Documentos</h2>
    <p class="sub">Sube tu informe o documento para revisión interna.</p>

    <!-- El formulario envía el archivo a upload.php mediante POST -->
    <!-- enctype="multipart/form-data" es obligatorio para subida de archivos -->
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <label>Selecciona un archivo:</label>
        <input type="file" name="archivo">
        <input type="submit" value="Subir documento">
    </form>

    <p class="notice">⚠️ Solo se permiten documentos corporativos. Uso interno.</p>
</div>
</body>
</html>
