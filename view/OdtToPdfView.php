



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convertir ODT a PDF</title>
</head>
<body>
<header>
    <h1>Convertidor de ODT a PDF</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo ODT para convertirlo a PDF:</h2>
        <form action="../controller/OdtToPdfController.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Convertir a PDF</button>
        </form>

    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Conversor de ODT a PDF</p>
</footer>
</body>
</html>
