var jsonUrl = '../controller/progress.json';

// Elementos de progreso
var progressText = document.getElementById('progressText');
var progressBar = document.getElementById('progressBar');

// Función para actualizar el progreso
function updateProgress() {
    fetch(jsonUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('No se pudo encontrar el archivo JSON');
            }
            return response.json();
        })
        .then(data => {
            var progress = 0;
            if (data.totalSteps > 0) {
                progress = (data.currentStep / data.totalSteps) * 100;
            }

            progressText.innerText = 'Progreso: ' + progress.toFixed(2) + '%';
            progressBar.value = progress;

            if (progress >= 100) {
                // Espera 5 segundos antes de mostrar el enlace de descarga
                setTimeout(function() {
                    document.getElementById('downloadLink').style.display = 'block';
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Función para enviar la solicitud de conversión de PDF
function convertPdf(event) {
    event.preventDefault();

    var formData = new FormData(event.target);
    var request = new XMLHttpRequest();

    request.open('POST', '../controller/PdfConverterController.php');
    request.send(formData);

    // Actualiza el progreso cada segundo
    setInterval(updateProgress, 1000);
}

// Añade el evento submit al formulario
document.querySelector('form').addEventListener('submit', convertPdf);