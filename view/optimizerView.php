<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Optimizador de PDF</title>
</head>
<body>
<h1>Optimizador de PDF</h1>
<form action="../controller/pdfOptimizerController.php" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Selecciona un PDF:</label>
    <input type="file" id="pdfFile" name="pdfFile" accept=".pdf"><br>
    <label for="quality">Calidad de salida:</label>
    <select id="quality" name="quality">
        <option value="baja">Baja</option>
        <option value="media">Media</option>
        <option value="alta">Alta</option>
    </select><br>
    <input type="submit" name="submit" value="Optimizar tamaño">
</form>
</body>
</html>