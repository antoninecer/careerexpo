<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $candidateId = $_SESSION['profile_id'];
    $fileId = (int)$_POST['file_id'];

    // Fetch and verify ownership
    $stmt = $pdo->prepare("SELECT * FROM candidate_files WHERE id = ? AND candidate_id = ?");
    $stmt->execute([$fileId, $candidateId]);
    $file = $stmt->fetch();

    if ($file) {
        // Delete physical file
        if (file_exists($file['filepath'])) {
            unlink($file['filepath']);
        }
        // Delete DB record
        $stmt = $pdo->prepare("DELETE FROM candidate_files WHERE id = ?");
        $stmt->execute([$fileId]);
        $_SESSION['flash_success'] = 'Životopis byl úspěšně smazán.';
    } else {
        $_SESSION['flash_error'] = 'Soubor nebyl nalezen nebo k němu nemáte přístup.';
    }
}

redirect('/profile_edit.php');
