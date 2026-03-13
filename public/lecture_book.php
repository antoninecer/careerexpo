<?php
require_once __DIR__ . '/../inc/bootstrap.php';

requireRole('candidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $lectureId = (int)$_POST['lecture_id'];
    $candidateId = $_SESSION['profile_id'];

    try {
        // Check capacity
        $stmt = $pdo->prepare("SELECT capacity, 
                                (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = ?) as occupancy 
                                FROM lectures WHERE id = ?");
        $stmt->execute([$lectureId, $lectureId]);
        $lecture = $stmt->fetch();

        if ($lecture && $lecture['occupancy'] < $lecture['capacity']) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO lecture_reservations (candidate_id, lecture_id) VALUES (?, ?)");
            $stmt->execute([$candidateId, $lectureId]);
            $_SESSION['flash_success'] = 'Rezervace přednášky byla úspěšná.';
        } else {
            $_SESSION['flash_error'] = 'Kapacita přednášky je již naplněna.';
        }
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při rezervaci: ' . $e->getMessage();
    }
}

redirect('/lectures.php');

