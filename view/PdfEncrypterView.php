<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Encriptar PDF</title>
</head>
<body>
<h1>Encriptar PDF</h1>
<form action="/controller/PdfEncryptController.php" method="post" enctype="multipart/form-data">
    <label for="pdf">Selecciona el archivo PDF:</label><br>
    <input type="file" id="pdf" name="pdf" accept="application/pdf"><br><br>

    <label for="password">Introduce la contraseña:</label><br>
    <input type="password" id="password" name="password"><br><br>

    <input type="submit" value="Encriptar PDF">
</form>
</body>
</html>
