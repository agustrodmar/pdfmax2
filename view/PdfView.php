<?php
/**
 * Verifica y visualiza un archivo PDF.
 */

// Verificar si se ha especificado un archivo
if (!isset($_GET['file'])) {
    echo "No se ha especificado un archivo PDF.";
    exit;
}

$pdfFile = __DIR__ . '/../' . $_GET['file'];

// Validar que el archivo existe y es un PDF
if (!file_exists($pdfFile) || mime_content_type($pdfFile) !== 'application/pdf') {
    echo "Archivo no válido.";
    exit;
}

$pdfFileUrl = htmlspecialchars($_GET['file']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver PDF</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        embed {
            width: 100%;
            height: 100vh; /* Vista completa del viewport */
        }
    </style>
</head>
<body>
<h1>Ver PDF</h1>
<embed src="../<?= $pdfFileUrl ?>" type="application/pdf" />
<script>
    // Eliminar el archivo PDF después de un retraso para asegurar que se haya cargado
    setTimeout(function() {
        fetch('../controller/PdfPresenterController.php?delete=true&file=<?= urlencode($pdfFileUrl) ?>')
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error(error));
    }, 5000);
</script>
</body>
</html>
