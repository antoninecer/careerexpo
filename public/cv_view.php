<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

$candidateId = $_SESSION['profile_id'];
$fileId = (int)($_GET['id'] ?? 0);
$download = isset($_GET['download']);

// Fetch file info
$stmt = $pdo->prepare("SELECT * FROM candidate_files WHERE id = ? AND candidate_id = ?");
$stmt->execute([$fileId, $candidateId]);
$file = $stmt->fetch();

if (!$file || !file_exists($file['filepath'])) {
    die('Soubor nebyl nalezen.');
}

$mime = mime_content_type($file['filepath']);
header('Content-Type: ' . $mime);
if ($download) {
    header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
} else {
    header('Content-Disposition: inline; filename="' . $file['filename'] . '"');
}
readfile($file['filepath']);
exit;
