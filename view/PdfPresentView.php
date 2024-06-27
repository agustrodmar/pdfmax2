<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentador de PDF</title>
</head>
<body>
<header>
    <h1>Presentador de PDF</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo PDF para visualizar:</h2>
        <form action="../controller/PdfPresenterController.php" method="post" enctype="multipart/form-data">
            <label for="pdfArchivo">Archivo PDF:</label>
            <input type="file" name="file" id="pdfArchivo" accept="application/pdf" required>
            <button type="submit">Visualizar PDF</button>
        </form>
    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Presentador de PDF</p>
</footer>

</body>
</html>
