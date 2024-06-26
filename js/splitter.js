// Configuración de la fuente del trabajador para pdf.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

/**
 * Inicializa el script cuando el DOM está completamente cargado.
 */
document.addEventListener("DOMContentLoaded", function() {
    let pageCount = 0;

    document.getElementById('pdf').addEventListener('change', function(event) {
        loadPdf(event.target);
    });

    /**
     * Carga el PDF y obtiene el número de páginas.
     * @param {HTMLInputElement} input - El input de archivo PDF.
     */
    function loadPdf(input) {
        try {
            const file = input.files[0];
            if (file) {
                const fileReader = new FileReader();
                fileReader.onload = function(event) {
                    try {
                        const typedarray = new Uint8Array(event.target.result);

                        pdfjsLib.getDocument({data: typedarray}).promise.then(function(pdf) {
                            pageCount = pdf.numPages;
                            document.getElementById('pageCount').innerText = 'Número de páginas: ' + pageCount;
                            document.getElementById('pageInfo').style.display = 'block';
                            updateRangeLimits();
                        }).catch(function(error) {
                            console.error("Error al cargar el PDF:", error);
                            alert("Error al cargar el PDF.");
                        });
                    } catch (error) {
                        console.error("Error al leer el archivo:", error);
                        alert("Error al leer el archivo.");
                    }
                };
                fileReader.readAsArrayBuffer(file);
            }
        } catch (error) {
            console.error("Error al procesar el archivo:", error);
            alert("Error al procesar el archivo.");
        }
    }

    document.getElementById('addRangeButton').addEventListener('click', function() {
        addRange();
    });

    /**
     * Añade un nuevo rango de páginas si los rangos anteriores están completos y válidos.
     */
    function addRange() {
        const rangesContainer = document.getElementById('rangesContainer');
        const rangeCount = rangesContainer.querySelectorAll('div.range').length + 1;
        const rangeDiv = document.createElement('div');
        rangeDiv.classList.add('range');

        rangeDiv.innerHTML = `<label for="start${rangeCount}">Rango ${rangeCount}:</label>
                              <input type="number" id="start${rangeCount}" name="ranges[${rangeCount - 1}][start]" min="1" placeholder="Inicio">
                              <label for="end${rangeCount}"> a </label>
                              <input type="number" id="end${rangeCount}" name="ranges[${rangeCount - 1}][end]" min="1" placeholder="Fin"><br><br>`;

        rangesContainer.appendChild(rangeDiv);
        updateRangeLimits();
    }

    /**
     * Actualiza los límites de los inputs de rango de páginas.
     */
    function updateRangeLimits() {
        const inputs = document.querySelectorAll('#rangesContainer input[type="number"]');
        inputs.forEach(input => {
            input.setAttribute('max', pageCount);
        });
    }
});
