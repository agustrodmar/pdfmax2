<?php

/**
 * Clase para convertir imágenes a un archivo PDF.
 */
class ImageToPdfModel
{
    /**
     * Convierte un conjunto de imágenes a un archivo PDF.
     *
     * @param array $imagePaths Rutas de las imágenes a convertir.
     * @param string $outputPdf Ruta del archivo PDF de salida.
     * @return string Ruta del archivo PDF creado.
     * @throws ImagickException
     */
    public function convertToPdf(array $imagePaths, string $outputPdf): string
    {
        // Crear una nueva instancia de Imagick
        $pdf = new Imagick();
        $pdf->setCompressionQuality(100); // Establecer la calidad de compresión al 100%

        // Iterar sobre cada ruta de imagen
        foreach ($imagePaths as $imagePath) {
            $image = new Imagick($imagePath); // Cargar la imagen
            $image->setImageFormat('pdf'); // Establecer el formato de la imagen como PDF
            $pdf->addImage($image); // Agregar la imagen al documento PDF

            // Limpiar y destruir la instancia de la imagen para liberar memoria
            $image->clear();
            $image->destroy();
        }

        // Escribir todas las imágenes en un solo archivo PDF
        $pdf->writeImages($outputPdf, true);

        // Limpiar y destruir la instancia del PDF para liberar memoria
        $pdf->clear();
        $pdf->destroy();

        return $outputPdf; // Devolver la ruta del archivo PDF creado
    }
}
