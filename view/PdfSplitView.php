<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dividir PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="../js/splitter.js" defer></script>
</head>
<body>
<h1>Dividir PDF</h1>
<form id="uploadForm" action="../controller/PdfSplitController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona el archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept="application/pdf"><br><br>

    <div id="pageInfo">
        <p id="pageCount">Número de páginas: 0</p>
    </div>

    <div id="rangesContainer">
        <div class="range">
            <label for="start1">Rango 1:</label>
            <input type="number" id="start1" name="ranges[0][start]" min="1" placeholder="Inicio">
            <label for="end1"> a </label>
            <input type="number" id="end1" name="ranges[0][end]" min="1" placeholder="Fin"><br><br>
        </div>
    </div>

    <button type="button" id="addRangeButton">Añadir Rango</button><br><br>

    <input type="submit" value="Dividir PDF">
</form>

<p>Para extraer páginas de un PDF <a href="ExtractorView.php">haz clic aquí</a>.</p>

</body>
</html>
