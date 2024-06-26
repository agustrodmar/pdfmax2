<?php
session_start();
require_once '../controller/metadataHandler/PdfMetaModifierController.php';

$controller = new PdfMetaModifierController();
$friendlyMetadata = [];
$error = '';
$filePath = '';

if (isset($_SESSION['filePath'])) {
    $filePath = $_SESSION['filePath'];
    try {
        if (isset($_SESSION['updatedFilePath'])) {
            $filePath = $_SESSION['updatedFilePath'];
        }

        $result = $controller->getAndRenderMetaData($filePath);
        $friendlyMetadata = $result['metadata'];
        $fileSize = $controller->model->getFileSize($filePath);
        $paperSize = $controller->model->getPaperSize($filePath);
        $containsJavaScript = $controller->model->containsJavaScript($filePath);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
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
<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
<form action="../controller/metadataHandler/UploadHandler.php" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Selecciona un PDF:</label>
    <input type="file" id="pdfFile" name="pdfFile" accept="application/pdf" required>
    <input type="submit" name="submit" value="Cargar PDF">
</form>

<?php if (!empty($friendlyMetadata)): ?>
    <h2>Metadatos Actuales:</h2>
    <?php foreach ($friendlyMetadata as $key => $value): ?>
        <p><strong><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endforeach; ?>
    <p><strong>Tamaño del Archivo:</strong> <?php echo htmlspecialchars($fileSize, ENT_QUOTES, 'UTF-8'); ?> bytes</p>
    <p><strong>Tamaño del Papel:</strong> <?php echo htmlspecialchars($paperSize, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Contiene JavaScript:</strong> <?php echo htmlspecialchars($containsJavaScript, ENT_QUOTES, 'UTF-8'); ?></p>
    <form action="../controller/metadataHandler/UpdateMetadata.php" method="post">
        <label for="author">Autor:</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($friendlyMetadata['Autor'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="title">Título:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($friendlyMetadata['Título'] ?? ($filePath ? pathinfo($filePath, PATHINFO_FILENAME) : ''), ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="subject">Tema:</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($friendlyMetadata['Tema'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <label for="keywords">Palabras Clave:</label>
        <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($friendlyMetadata['Palabras Clave'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br>
        <input type="hidden" name="filePath" value="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="submit" name="submit" value="Guardar Cambios">
    </form>
    <?php if (isset($_SESSION['updatedFilePath'])): ?>
        <a href="../utils/metadata/DownloadMetadata.php">Descargar PDF Actualizado</a>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
