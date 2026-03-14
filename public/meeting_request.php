<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

$eventId = getCurrentEventId();
$candidateId = $_SESSION['profile_id'];
$jobId = (int)($_GET['job_id'] ?? 0);

if (!$jobId) {
    redirect('/dashboard.php');
}

// Fetch job and company info
$stmt = $pdo->prepare("SELECT j.*, cp.id as company_id, cp.name as company_name 
                      FROM jobs j 
                      JOIN company_profiles cp ON j.company_id = cp.id 
                      WHERE j.id = ? AND j.event_id = ?");
$stmt->execute([$jobId, $eventId]);
$job = $stmt->fetch();

if (!$job) {
    $_SESSION['flash_error'] = 'Pozice nebyla nalezena.';
    redirect('/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $suggestedAt = $_POST['suggested_at'];
    $notes = $_POST['notes'];

    try {
        $stmt = $pdo->prepare("INSERT INTO meetings (candidate_id, company_id, job_id, event_id, suggested_at, status, notes) 
                            VALUES (?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([$candidateId, $job['company_id'], $jobId, $eventId, $suggestedAt, $notes]);
        
        $_SESSION['flash_success'] = 'Žádost o schůzku byla odeslána.';
        redirect('/meetings.php');
    } catch (Exception $e) {
        $error = 'Chyba při odesílání žádosti: ' . $e->getMessage();
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border-0">
            <h2 class="mb-3 fw-bold text-primary">Požádat o schůzku</h2>
            <div class="mb-4">
                <p class="mb-1 text-muted small">Firma</p>
                <h5 class="fw-bold"><?= e($job['company_name']) ?></h5>
                <p class="mb-1 text-muted small mt-3">Pozice</p>
                <h6 class="fw-bold"><?= e($job['title']) ?></h6>
            </div>
            <hr class="my-4">
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger small"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Navrhovaný čas setkání</label>
                    <input type="datetime-local" name="suggested_at" class="form-control rounded-pill" required>
                    <div class="form-text small">Navrhněte čas v rámci konání veletrhu.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Zpráva pro firmu / Poznámka</label>
                    <textarea name="notes" class="form-control" rows="3" style="border-radius: 15px;" placeholder="Dobrý den, rád bych se u vás zastavil na stánku a probral tuto pozici..."></textarea>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill btn-lg shadow">Odeslat žádost</button>
                    <a href="/company_detail.php?id=<?= $job['company_id'] ?>" class="btn btn-outline-secondary rounded-pill">Zpět</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
