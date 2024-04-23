<?php
require_once __DIR__ . '/../model/PdfMetaModifierModel.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('',1);

class PdfMetaModifierController {

    private PdfMetaModifierModel $model;

    public function __construct() {
        $this->model = new PdfMetaModifierModel();
    }

    public function handleFileUpload($file): ?string
    {
        $uploadDir = sys_get_temp_dir();
        $uploadPath = $uploadDir . '/' . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        } else {
            return null;
        }
    }

    public function getAndShowMetaData($filePath): bool|string|null
    {
        return $this->model->getMetaData($filePath);
    }

    public function updateAndSaveMetaData($filePath, $postData): string
    {
        $newMetaData = "InfoKey: Author\nInfoValue: " . $postData['author'] . "\n"
            . "InfoKey: Title\nInfoValue: " . $postData['title'] . "\n"
            . "InfoKey: Subject\nInfoValue: " . $postData['subject'] . "\n"
            . "InfoKey: Keywords\nInfoValue: " . $postData['keywords'] . "\n"
            . "InfoKey: ModDate\nInfoValue: " . date("YmdHis") . "\n";
        return $this->model->updateMetaData($filePath, $newMetaData);
    }
}

$controller = new PdfMetaModifierController();

