

var operationId = document.body.getAttribute('data-operation-id');
var jsonUrl = '../tmps/' + operationId + '_progress.json';
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
                setTimeout(function() {
                    document.getElementById('downloadLink').style.display = 'block';
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Si el archivo JSON no se encuentra, simplemente ignora el error y sigue intentando
            if (error.message === 'No se pudo encontrar el archivo JSON') {
                console.log('El archivo de progreso aún no se ha creado. Seguir intentando...');
            }
        });
}
// Función para enviar la solicitud de conversión de PDF
function convertPdf(event) {
    event.preventDefault();

    var formData = new FormData(event.target);
    var request = new XMLHttpRequest();

    request.open('POST', '../controller/PdfConverterController.php');
    request.send(formData);

    // Espera 2 segundos antes de comenzar a actualizar el progreso
    setTimeout(function() {
        setInterval(updateProgress, 1000);
    }, 2000);
}

// Añade el evento submit al formulario
document.querySelector('form').addEventListener('submit', convertPdf);