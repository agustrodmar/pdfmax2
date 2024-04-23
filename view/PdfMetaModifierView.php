
<?php
require_once '../controller/PdfMetaModifierController.php';


?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Modificar Metadatos PDF</title>
</head>
<body>
<h1>Modificar Metadatos PDF</h1>
<form action="../controller/metadataHandler/UploadHandler.php" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Selecciona un PDF:</label>
    <input type="file" id="pdfFile" name="pdfFile">
    <input type="submit" name="submit" value="Cargar PDF">
</form>

<?php if (isset($_SESSION['metadata'])): ?>
    <h2>Metadatos Actuales:</h2>
    <pre><?php echo htmlspecialchars(print_r($_SESSION['metadata'], true)); ?></pre>
    <form action="../controller/metadataHandler/UpdateMetadata.php" method="post">
        <label for="author">Autor:</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_SESSION['metadata']['Info']['Author'] ?? ''); ?>"><br>
        <label for="title">Título:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_SESSION['metadata']['Info']['Title'] ?? ''); ?>"><br>
        <label for="subject">Tema:</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_SESSION['metadata']['Info']['Subject'] ?? ''); ?>"><br>
        <label for="keywords">Palabras Clave:</label>
        <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($_SESSION['metadata']['Info']['Keywords'] ?? ''); ?>"><br>
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
<?php endif; ?>

</body>
</html>

