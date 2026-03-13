<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $lectureId = (int)$_POST['lecture_id'];
    $candidateId = $_SESSION['profile_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM lecture_reservations WHERE candidate_id = ? AND lecture_id = ?");
        $stmt->execute([$candidateId, $lectureId]);
        $_SESSION['flash_success'] = 'Rezervace přednášky byla úspěšně zrušena.';
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při rušení rezervace: ' . $e->getMessage();
    }
}

redirect('/lectures.php');
