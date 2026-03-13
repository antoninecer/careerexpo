<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $meetingId = (int)$_POST['meeting_id'];
    $outcome = $_POST['outcome'];
    $candidateId = $_SESSION['profile_id'];

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM meetings WHERE id = ? AND candidate_id = ?");
    $stmt->execute([$meetingId, $candidateId]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE meetings SET outcome = ? WHERE id = ?");
        $stmt->execute([$outcome, $meetingId]);
        $_SESSION['flash_success'] = 'Děkujeme za zpětnou vazbu! Organizátoři vidí váš úspěch.';
    } else {
        $_SESSION['flash_error'] = 'Neautorizovaná akce.';
    }
}

redirect('/meetings.php');
