<?php
require_once __DIR__ . '/../inc/bootstrap.php';

requireRole('candidate');

$candidateId = $_SESSION['profile_id'];
$jobId = (int)($_GET['job_id'] ?? 0);

if (!$jobId) {
    redirect('/dashboard.php');
}

// Fetch job and company
$stmt = $pdo->prepare("SELECT j.*, c.id as company_id, c.name as company_name 
                      FROM jobs j 
                      JOIN company_profiles c ON j.company_id = c.id 
                      WHERE j.id = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    redirect('/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $suggestedAt = $_POST['suggested_at'];
    $notes = $_POST['notes'];

    try {
        $stmt = $pdo->prepare("INSERT INTO meetings (candidate_id, company_id, job_id, suggested_at, status, notes) 
                            VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([$candidateId, $job['company_id'], $jobId, $suggestedAt, $notes]);
        
        $_SESSION['flash_success'] = 'Žádost o schůzku byla odeslána.';
        redirect('/dashboard.php');
    } catch (Exception $e) {
        $error = 'Chyba: ' . $e->getMessage();
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h2 class="mb-3">Požádat o schůzku</h2>
            <p><strong>Firma:</strong> <?= e($job['company_name']) ?></p>
            <p><strong>Pozice:</strong> <?= e($job['title']) ?></p>
            <hr>
            
            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label">Navrhovaný čas</label>
                    <input type="datetime-local" name="suggested_at" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Zpráva pro firmu / Poznámka</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Dobrý den, rád bych se u vás zastavil na stánku..."></textarea>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">Odeslat žádost</button>
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Zrušit</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

