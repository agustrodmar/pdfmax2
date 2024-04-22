<?php

require_once(__DIR__ . '/../model/PdfToTextModel.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class pdfToTextController {
    private $model;

    public function __construct() {
        $this->model = new pdfToTextModel();
    }

    public function convert($file, $format) {
        if ($format == 'txt') {
            return $this->model->convertToText($file);
        } else if ($format == 'odt') {
            return $this->model->convertToOdt($file);
        }
    }
}

