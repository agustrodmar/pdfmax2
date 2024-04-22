<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extractor de páginas de PDF</title>
</head>
<body>
<header>
    <h1>Extractor de páginas de PDF</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo PDF y especifique las páginas que desea extraer:</h2>
        <form action="../controller/PdfExtractorController.php" method="post" enctype="multipart/form-data">
            <label for="pdfArchivo">Archivo PDF:</label>
            <input type="file" name="pdfArchivo" id="pdfArchivo" required>
            <label for="paginas">Páginas a extraer (separadas por comas):</label>
            <input type="text" name="paginas" id="paginas" placeholder="Ej: 1, 4, 8, 9" required>
            <label for="outputPath">Ruta de salida:</label>
            <input type="text" name="outputPath" id="outputPath" placeholder="Ruta donde se guardará el archivo PDF de salida" required>
            <label for="outputFileName">Nombre del archivo de salida:</label>
            <input type="text" name="outputFileName" id="outputFileName" placeholder="Nombre del archivo PDF de salida" required>
            <button type="submit">Extraer páginas</button>
        </form>
    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Extractor de páginas de PDF</p>
</footer>

</body>
</html>