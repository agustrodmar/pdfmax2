var jsonUrl = '/controller/progress.php'; // Endpoint para recuperar el progreso de la sesión

// Elementos de progreso
var progressText = document.getElementById('progressText');
var progressBar = document.getElementById('progressBar');
var progressInterval; // Variable para mantener la referencia del intervalo

document.querySelector('form').addEventListener('submit', async (event) => {
    event.preventDefault();

    // Resetea la barra de progreso y el texto al iniciar
    progressBar.value = 0;
    progressText.innerText = 'Progreso: 0%';

    // Limpia el intervalo existente si hay alguno
    if (progressInterval) {
        clearInterval(progressInterval);
    }

    const formData = new FormData(event.target);
    try {
        const response = await fetch('../controller/PdfConverterController.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) throw new Error('Network response was not ok.');

        // Inicia la actualización del progreso tras iniciar la conversión
        progressInterval = setInterval(updateProgress, 1000);
    } catch (error) {
        console.error('Error:', error);
    }
});

async function updateProgress() {
    try {
        const response = await fetch(`${jsonUrl}?t=${new Date().getTime()}`);
        const data = await response.json();
        var progress = (data.currentStep / data.totalSteps) * 100;
        progressText.innerText = 'Progreso: ' + progress.toFixed(2) + '%';
        progressBar.value = progress;

        if (progress >= 100) {
            clearInterval(progressInterval); // Limpia el intervalo una vez que se completa
            setTimeout(() => {
                document.getElementById('downloadLink').style.display = 'block';
            }, 5000);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Función para enviar la solicitud de conversión de PDF
function convertPdf(event) {
    event.preventDefault();

    // Resetea la barra de progreso y el texto al iniciar
    progressBar.value = 0;
    progressText.innerText = 'Progreso: 0%';

    var formData = new FormData(event.target);
    var request = new XMLHttpRequest();

    request.open('POST', '../controller/PdfConverterController.php');
    request.send(formData);

    // Actualiza el progreso cada segundo
    setInterval(updateProgress, 1000);
}

// Añade el evento submit al formulario
document.querySelector('form').addEventListener('submit', convertPdf);