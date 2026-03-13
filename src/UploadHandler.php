<?php
namespace App;

class UploadHandler {
    private $uploadDir;
    private $allowedMimes = [
        'application/pdf',
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
    ];
    private $allowedExtensions = ['pdf', 'doc', 'docx'];
    private $maxSize = 5242880; // 5MB

    public function __construct($uploadDir) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    public function upload($file, $candidateId) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Chyba při nahrávání souboru.');
        }

        // Check size
        if ($file['size'] > $this->maxSize) {
            throw new \Exception('Soubor je příliš velký (max 5MB).');
        }

        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            throw new \Exception('Nepovolená přípona souboru (pouze PDF, DOC, DOCX).');
        }

        // Check MIME type via finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $this->allowedMimes)) {
            error_log("Upload blocked: Illegal MIME type $mime for file {$file['name']}");
            throw new \Exception('Nepovolený obsah souboru.');
        }

        // Secure filename
        $safeName = 'cv_' . $candidateId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target = $this->uploadDir . $safeName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return [
                'filename' => $file['name'], // Original name for display
                'filepath' => $target
            ];
        }

        throw new \Exception('Nepodařilo se uložit soubor.');
    }
}
