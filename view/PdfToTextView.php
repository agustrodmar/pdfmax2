<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor de PDF</title>
</head>
<body>
<header>
    <h1>Conversor de PDF a Texto o ODT</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo PDF y el formato de salida:</h2>
        <form action="../controller/PdfToTextController.php" method="post" enctype="multipart/form-data">
            <label for="pdfArchivo">Archivo PDF:</label>
            <input type="file" name="pdfArchivo" id="pdfArchivo" required>
            <label for="formato">Formato de salida:</label>
            <select name="formato" id="formato" required>
                <option value="">--Por favor elija un formato--</option>
                <option value="txt">Texto (TXT)</option>
                <option value="odt">OpenDocument (ODT)</option>
            </select>
            <button type="submit">Convertir</button>
        </form>
    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Conversor de PDF a Texto o ODT</p>
</footer>

</body>
</html>
