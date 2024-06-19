<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Optimizador de PDF</title>
</head>
<body>
<h1>Optimizador de PDF</h1>
<form action="../controller/PdfOptimizerController.php" method="post" enctype="multipart/form-data">
    <label for="pdfFile">Selecciona un PDF:</label>
    <input type="file" id="pdfFile" name="pdfFile" accept=".pdf"><br>
    <label for="quality">Selecciona la calidad:</label>
    <select id="quality" name="quality">
        <option value="screen">Baja</option>
        <option value="ebook">Media</option>
        <option value="printer">Alta</option>
    </select><br>
    <input type="submit" name="submit" value="Optimizar tamaño">
</form>
</body>
</html>
