<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor de Texto a PDF</title>
    <script>
        function togglePageField() {
            const fileInput = document.getElementById('textArchivo');
            const pagesField = document.getElementById('paginas');
            const file = fileInput.files[0];
            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (fileExtension === 'txt') {
                    pagesField.disabled = true;
                    pagesField.value = '';
                } else {
                    pagesField.disabled = false;
                }
            }
        }
    </script>
</head>
<body>
<header>
    <h1>Conversor de Texto a PDF</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo de texto para convertir a PDF:</h2>
        <form action="../controller/TextToPdfController.php" method="post" enctype="multipart/form-data">
            <label for="textArchivo">Archivo de Texto:</label>
            <input type="file" name="file" id="textArchivo" accept=".odt,.docx,.txt" required onchange="togglePageField()">
            <label for="paginas">Páginas:</label>
            <input type="text" name="pages" id="paginas" placeholder="Ejemplo: 1,2,3 o 1-3">
            <p>Ingrese las páginas a extraer en un formato válido. Ejemplo: 1-3 o 1,2,3</p>
            <button type="submit">Convertir</button>
        </form>
    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Conversor de Texto a PDF</p>
</footer>

</body>
</html>
