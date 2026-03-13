<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$role = $_SESSION['user_role'];
$profileId = $_SESSION['profile_id'];
$code = strtoupper(trim($_POST['pairing_code'] ?? ''));

if (empty($code)) {
    redirect('/dashboard.php');
}

if ($role === 'company') {
    // Company scanning candidate
    $stmt = $pdo->prepare("SELECT id FROM candidate_profiles WHERE pairing_code = ?");
    $stmt->execute([$code]);
    $candidate = $stmt->fetch();

    if ($candidate) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO profile_connections (candidate_id, company_id, status) VALUES (?, ?, 'maybe')");
        $stmt->execute([$candidate['id'], $profileId]);
        redirect('/candidate_detail.php?id=' . $candidate['id']);
    } else {
        $_SESSION['flash_error'] = 'Kód kandidáta nebyl nalezen.';
        redirect('/dashboard.php');
    }
} elseif ($role === 'candidate') {
    // Candidate scanning company
    $stmt = $pdo->prepare("SELECT id FROM company_profiles WHERE pairing_code = ?");
    $stmt->execute([$code]);
    $company = $stmt->fetch();

    if ($company) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO profile_connections (candidate_id, company_id, status) VALUES (?, ?, 'maybe')");
        $stmt->execute([$profileId, $company['id']]);
        redirect('/company_detail.php?id=' . $company['id']);
    } else {
        $_SESSION['flash_error'] = 'Kód firmy nebyl nalezen.';
        redirect('/dashboard.php');
    }
} else {
    redirect('/dashboard.php');
}

