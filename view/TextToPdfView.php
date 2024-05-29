<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convertir ODT a PDF</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("conversionForm").onsubmit = function() {
                var fileInput = document.querySelector('input[type="file"]');
                var fileSize = fileInput.files[0].size;
                var maxFileSize = 2 * 1024 * 1024; // 2MB
                if (fileSize > maxFileSize) {
                    alert("El archivo no debe superar los 2MB.");
                    return false;
                }
                return true;
            };
        });
    </script>
</head>
<body>
<header>
    <h1>Convertidor de Texto txt, docx y odt, a PDF</h1>
</header>

<main>
    <section>
        <h2>Seleccione un archivo ODT para convertirlo a PDF:</h2>
        <form id="conversionForm" action="../controller/TextToPdfController.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <label>
                <p>Selecciona el número de páginas que desea transformar a PDF: </p>
                <input type="text" name="pages" placeholder="Ej: 1, 2-4, 7" required>
            </label>
            <button type="submit">Convertir a PDF</button>
        </form>
    </section>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> Conversor de ODT a PDF</p>
</footer>
</body>
</html>
