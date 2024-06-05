<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir y Visualizar PDF</title>
</head>
<body>
<h1>Subir y Visualizar PDF</h1>
<form action="../controller/PdfPresenterController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona el archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept="application/pdf"><br><br>
    <input type="submit" value="Subir y Ver">
</form>
</body>
</html>
