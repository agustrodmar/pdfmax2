<!DOCTYPE html>
<html>
<body>

<form action="../controller/DocxToPdfController.php" method="post" enctype="multipart/form-data">
    Selecciona un archivo DOCX para convertir a PDF:
    <input type="file" name="docxFile" id="docxFile">
    <input type="submit" value="Convertir" name="submit">
</form>

</body>
</html>
