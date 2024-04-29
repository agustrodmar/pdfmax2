
<?php
require_once '../controller/PdfMetaModifierController.php';
require_once '../controller/metadataHandler/MetadataFormatter.php';

$friendlyMetadata = [];
if (isset($_SESSION['metadata'])) {
    $friendlyMetadata = MetadataFormatter::getFriendlyMetadata($_SESSION['metadata']);
}
?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <title>Modificar Metadatos PDF</title>
</head>
<body>
<h1>Modificar Metadatos PDF</h1>
<form action="../controller/metadataHandler/UploadHandler.php" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Selecciona un PDF:</label>
    <input type="file" id="pdfFile" name="pdfFile">
    <input type="submit" name="submit" value="Cargar PDF">
</form>

<?php if (!empty($friendlyMetadata)): ?>
    <h2>Metadatos Actuales:</h2>
    <?php foreach ($friendlyMetadata as $key => $value): ?>
        <p><strong><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endforeach; ?>
    <form action="../controller/metadataHandler/UpdateMetadata.php" method="post">
        <label for="author">Autor:</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($friendlyMetadata['Autor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="title">Título:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($friendlyMetadata['Título'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="subject">Tema:</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($friendlyMetadata['Tema'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="keywords">Palabras Clave:</label>
        <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($friendlyMetadata['Palabras Clave'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
    <?php if (isset($_SESSION['downloadPath'])): ?>
        <a href="../utils/downloadMetadaData.php">Descargar PDF Actualizado</a>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>

