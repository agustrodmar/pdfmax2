<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convertir Imágenes a PDF</title>
</head>
<body>
<h1>Convertir Imágenes a PDF</h1>
<form action="../controller/ImageToPdfController.php" method="post" enctype="multipart/form-data">
    <label for="images">Selecciona las imágenes:</label><br>
    <input type="file" id="images" name="images[]" accept="image/jpeg, image/png" multiple><br><br>
    <input type="submit" value="Convertir a PDF">
</form>
</body>
</html>
