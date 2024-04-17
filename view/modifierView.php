
<?php
require_once '../controller/pdfModifierController.php';

echo "Archivo PDFModifierController.php incluido correctamente";

?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Modificar Metadatos PDF</title>
</head>
<body>
<h1>Modificar Metadatos PDF</h1>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Si se envía un pdf...
if (isset($_FILES['pdfFile'])) {
    $pdfPath = $_FILES['pdfFile']['tmp_name'];
    $pdfModifierController = new PDFModifierController();

    // para obtener los metadatos del pdf
    $metadata = $pdfModifierController->getPDFMetadata($pdfPath);
    echo "<h2>Metadatos Actuales:</h2>";
    echo "<pre>" . print_r($metadata, true) . "</pre>";

    // Si se han enviado nuevos metadatos
    if (isset($_POST['submit'])) {
        $newMetadata = array(
            'Author' => $_POST['author'] ?? $metadata['Info']['Author'] ?? '',
            'Title' => $_POST['title'] ?? $metadata['Info']['Title'] ?? '',
            'Subject' => $_POST['subject'] ?? $metadata['Info']['Subject'] ?? '',
            'Keywords' => $_POST['keywords'] ?? $metadata['Info']['Keywords'] ?? ''
            // Puedes añadir más campos de metadatos aquí
        );

        // Actualizo los metadatos del PDF
        $pdfModifierController->updatePDFMetadata($pdfPath, $newMetadata);

        // llama a mensaje de éxito
        echo "<p>Metadatos actualizados correctamente.</p>";
    }
    ?>
    <form action="" method="post">
        <label for="author">Autor:</label>
        <input type="text" id="author" name="author" value="<?php echo $metadata['Info']['Author'] ?? ''; ?>"><br>
        <label for="title">Título:</label>
        <input type="text" id="title" name="title" value="<?php echo $metadata['Info']['Title'] ?? ''; ?>"><br>
        <label for="subject">Tema:</label>
        <input type="text" id="subject" name="subject" value="<?php echo $metadata['Info']['Subject'] ?? ''; ?>"><br>
        <label for="keywords">Palabras Clave:</label>
        <input type="text" id="keywords" name="keywords" value="<?php echo $metadata['Info']['Keywords'] ?? ''; ?>"><br>
        <!-- Puedes añadir más campos de metadatos aquí -->
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
    <?php
} else {
    // Si no se ha enviado un archivo PDF, muestro el formulario para cargarlo
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="pdfFile">Selecciona un PDF:</label>
        <input type="file" id="pdfFile" name="pdfFile">
        <input type="submit" name="submit" value="Cargar PDF">
    </form>
    <?php
}
?>
</body>
</html>
