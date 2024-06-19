/**
 * Inicializa el script cuando el DOM está completamente cargado.
 */
document.addEventListener("DOMContentLoaded", function() {
    let pageCount = 0;

    /**
     * Maneja el evento de cambio en el input de archivo PDF.
     * @param {Event} event - El evento de cambio.
     */
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
                fileReader.onload = function() {
                    try {
                        const typedarray = new Uint8Array(this.result);

                        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
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

    /**
     * Maneja el evento de clic en el botón para añadir un nuevo rango.
     */
    document.getElementById('addRangeButton').addEventListener('click', function() {
        addRange();
    });

    /**
     * Añade un nuevo rango de páginas.
     */
    function addRange() {
        try {
            const rangesContainer = document.getElementById('rangesContainer');
            const rangeCount = rangesContainer.children.length / 3 + 1; // 3 children per range
            const rangeDiv = document.createElement('div');

            rangeDiv.innerHTML = `<label for="range${rangeCount}">Rango ${rangeCount}:</label>
                                  <input type="number" id="start${rangeCount}" name="ranges[${rangeCount - 1}][start]" min="1" placeholder="Inicio">
                                  <input type="number" id="end${rangeCount}" name="ranges[${rangeCount - 1}][end]" min="1" placeholder="Fin"><br><br>`;

            rangesContainer.appendChild(rangeDiv);
            updateRangeLimits();
        } catch (error) {
            console.error("Error al añadir un nuevo rango:", error);
            alert("Error al añadir un nuevo rango.");
        }
    }

    /**
     * Actualiza los límites de los inputs de rango de páginas.
     */
    function updateRangeLimits() {
        try {
            const inputs = document.querySelectorAll('#rangesContainer input[type="number"]');
            inputs.forEach(input => {
                input.setAttribute('max', pageCount);
            });
        } catch (error) {
            console.error("Error al actualizar los límites de los rangos:", error);
            alert("Error al actualizar los límites de los rangos.");
        }
    }
});
