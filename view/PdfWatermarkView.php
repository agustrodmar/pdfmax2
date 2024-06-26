<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Marca de Agua</title>
</head>
<body>
<h1>Añadir Marca de Agua a PDF</h1>
<form action="../controller/PdfWatermarkController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona el archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept="application/pdf"><br><br>
    <label for="watermark">Selecciona la marca de agua (PDF):</label><br>
    <input type="file" id="watermark" name="watermark" accept="application/pdf"><br><br>
    <input type="submit" value="Añadir Marca de Agua">
</form>
</body>
</html>
