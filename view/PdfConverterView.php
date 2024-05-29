<html lang="es">
<head>
    <title>Conversor de PDF</title>
</head>
<body>
<h1>Conversor de PDF a Imagen</h1>
<form action="../controller/PdfConverterController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona un archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept=".pdf"><br><br>

    <label for="format">Selecciona el formato de salida:</label><br>
    <select id="format" name="format">
        <option value="svg">SVG</option>
        <option value="png">PNG</option>
        <option value="jpeg">JPEG</option>
    </select><br><br>

    <label for="pages">Especifica las páginas (ej. 1,3-5,7):</label><br>
    <input type="text" id="pages" name="pages"><br><br>
    <input type="submit" value="Convertir">

    <p id="progressText"></p>
    <progress id="progressBar" value="0" max="100"></progress>
    <p id="downloadMessage" style="display: none;">
        La conversión ha terminado. <a id="downloadLink" href="#" style="display: none;">Descarga tus imágenes aquí</a>
    </p>


    <script src="../js/PdfConversionProgress.js"></script>

</form>
</body>
</html>