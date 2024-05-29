document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const downloadLink = document.getElementById('downloadLink');
    const downloadMessage = document.getElementById('downloadMessage');
    const jsonUrl = '/var/www/html/pdfmax2/controller/progress.php'; // Endpoint para recuperar el progreso

    form.addEventListener('submit', async function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del formulario
        const uniqueId = 'pdf_convert_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        console.log('uniqueId generado:', uniqueId); // Registro de consola agregado
        const formData = new FormData(form);
        formData.append('uniqueId', uniqueId);

        try {
            const response = await fetch('/var/www/html/pdfmax2/controller/PdfConverterController.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (response.ok && result.success) {
                console.log('Inicio de sondeo de progreso para uniqueId:', uniqueId);
                startProgressPolling(uniqueId);
            } else {
                throw new Error('Network response was not ok or conversion failed');
            }
        } catch (error) {
            console.error('Error:', error);
            progressText.innerText = 'Error al procesar la conversión.';
        }
    });

    function updateDownloadLink(uniqueId) {
        const downloadUrl = `/var/www/html/pdfmax2/controller/downloadScript.php?uniqueId=${encodeURIComponent(uniqueId)}`;
        console.log('updateDownloadLink uniqueId:', uniqueId); // Registro de consola agregado
        downloadLink.href = downloadUrl;
        downloadMessage.style.display = 'block';
        downloadLink.style.display = 'inline';
    }

    function resetProgressUI() {
        progressBar.value = 0;
        progressText.innerText = 'Progreso: 0%';
        downloadLink.style.display = 'none';
    }

    async function startProgressPolling(uniqueId) {
        console.log('Iniciando la consulta de progreso para:', uniqueId);
        const intervalId = setInterval(async () => {
            try {
                const progressResponse = await
                    fetch(`${jsonUrl}?uniqueId=${encodeURIComponent(uniqueId)}&t=${new Date().getTime()}`);
                const progressData = await progressResponse.json();
                console.log('Datos de progreso recibidos:', progressData);

                const totalSteps = progressData.totalSteps ?? 0;
                const currentStep = progressData.currentStep ?? 0;

                const progress = totalSteps > 0 ? (currentStep / totalSteps) * 100 : 0;

                progressBar.value = progress;
                progressText.innerText = `Progreso: ${progress.toFixed(2)}%`;

                if (progress >= 100) {
                    clearInterval(intervalId);
                    updateDownloadLink(uniqueId);
                }
            } catch (error) {
                console.error('Error al obtener el progreso:', error);
                clearInterval(intervalId);
                progressText.innerText = 'Error al obtener el progreso.';
            }
        }, 1000);
    }
});
