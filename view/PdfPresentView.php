<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver PDF</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        embed {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
<h1>Ver PDF</h1>
<form id="pdfForm" action="../controller/PdfPresenterController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona el archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept="application/pdf"><br><br>
    <input type="submit" value="Visualizar PDF">
</form>
<embed id="pdfViewer" src="" type="application/pdf" style="display:none;" />
<script src="../js/pdfViewer.js"></script>
</body>
</html>
