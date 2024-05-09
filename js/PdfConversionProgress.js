document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const downloadLink = document.getElementById('downloadLink');
    const jsonUrl = '/controller/progress.php'; // Endpoint para recuperar el progreso

    form.addEventListener('submit', async function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del formulario
        const uniqueId = 'pdf_convert_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        const formData = new FormData(form);
        formData.append('uniqueId', uniqueId);

        try {
            const response = await fetch('../controller/PdfConverterController.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (response.ok && result.success) {
                startProgressPolling(uniqueId); // Asegúrate de pasar el uniqueId aquí
                updateDownloadLink(uniqueId); // Actualiza el enlace de descarga
            } else {
                throw new Error('Network response was not ok or conversion failed');
            }
        } catch (error) {
            console.error('Error:', error);
            progressText.innerText = 'Error al procesar la conversión.';
        }
    });

    function updateDownloadLink(uniqueId) {
        const downloadUrl = `../controller/downloadScript.php?uniqueId=${encodeURIComponent(uniqueId)}`;
        document.getElementById('downloadUrl').href = downloadUrl; // Asegúrate de que 'downloadUrl' es el ID correcto para tu enlace.
        document.getElementById('downloadLink').style.display = 'block';
    }

    function resetProgressUI() {
        progressBar.value = 0;
        progressText.innerText = 'Progreso: 0%';
        downloadLink.style.display = 'none';
    }

    function startProgressPolling(uniqueId) {
        const intervalId = setInterval(async () => {
            try {
                const progressResponse = await fetch(`${jsonUrl}?uniqueId=${encodeURIComponent(uniqueId)}&t=${new Date().getTime()}`);
                const progressData = await progressResponse.json();

                const progress = progressData.totalSteps > 0 ?
                    (progressData.currentStep / progressData.totalSteps) * 100 : 0;

                progressBar.value = progress;
                progressText.innerText = `Progreso: ${progress.toFixed(2)}%`;

                if (progress >= 100) {
                    clearInterval(intervalId);
                    downloadLink.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching progress:', error);
                clearInterval(intervalId);
            }
        }, 1000);
    }
});
