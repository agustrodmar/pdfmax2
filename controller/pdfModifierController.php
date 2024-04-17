<?php
require_once '../model/pdfModifierModel.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('',1);
require_once '../model/pdfModifierModel.php';

class PDFModifierController {

    /**
     * @return void Este método sirve para cargar la vista, aparentemente el IDE indica que no
     * se usa. TODO: Corregir eso.
     */
    public function showModifierPage(): void {
        // Lógica para mostrar la página de modificación
        include '../view/modifierView.php';
    }

    /**
     * @throws Exception
     */
    public function getPDFMetadata($pdfPath): array {
        $pdfModifier = new pdfModifierModel();
        return $pdfModifier->getMetadata($pdfPath);
    }



    public function updatePDFMetadata($pdfPath, $metadata): void
    {
        $pdfModifier = new pdfModifierModel();
        $pdfModifier->updateMetadata($pdfPath, $metadata);
        // Redireccionar o mostrar mensaje de éxito
    }
}
