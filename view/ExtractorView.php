<!DOCTYPE html>
<html lang="es">
<head>
    <title>Extractor de PDF</title>
</head>
<body>
<h1>Extractor de Páginas de PDF</h1>
<form action="../controller/PdfExtractorController.php" method="post" enctype="multipart/form-data">
    <label for="pdfArchivo">Selecciona un archivo PDF:</label><br>
    <input type="file" id="pdfArchivo" name="pdfArchivo" accept=".pdf"><br><br>

    <label for="paginas">Especifica las páginas (ej. 1,3-5,7):</label><br>
    <input type="text" id="paginas" name="paginas"><br><br>

    <label>Elige el modo de descarga:</label><br>
    <input type="radio" id="single" name="downloadMode" value="single" checked>
    <label for="single">Un solo documento PDF</label><br>
    <input type="radio" id="multiple" name="downloadMode" value="multiple">
    <label for="multiple">Documentos PDF separados (ZIP)</label><br><br>

    <input type="submit" value="Extraer y Descargar">
</form>
</body>
</html>
