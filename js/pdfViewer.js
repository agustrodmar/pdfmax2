document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('pdfForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.fileUrl) {
                    const pdfViewer = document.getElementById('pdfViewer');
                    pdfViewer.src = data.fileUrl;
                    pdfViewer.style.display = 'block';
                    setTimeout(() => {
                        fetch(`../controller/PdfPresenterController.php?delete=true&file=${encodeURIComponent(data.fileUrl)}`)
                            .then(response => response.text())
                            .then(data => console.log(data))
                            .catch(error => console.error(error));
                    }, 5000);
                } else {
                    console.error('No se ha podido obtener la URL del archivo PDF.');
                }
            })
            .catch(error => console.error('Error al cargar el PDF:', error));
    });
});
