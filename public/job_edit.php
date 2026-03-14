<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');
requireEvent();

$eventId = getCurrentEventId();
$companyId = $_SESSION['profile_id'];
$jobId = (int)($_GET['id'] ?? 0);

if (!$jobId) {
    redirect('/dashboard.php');
}

// Fetch job and verify ownership
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND company_id = ?");
$stmt->execute([$jobId, $companyId]);
$job = $stmt->fetch();

if (!$job) {
    $_SESSION['flash_error'] = 'Pozice nebyla nalezena nebo nemáte oprávnění ji upravovat.';
    redirect('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $seniority = $_POST['seniority'];
    $location = $_POST['location'];
    $collaboration_type = $_POST['collaboration_type'];
    $languages = $_POST['languages'];
    $priority = $_POST['priority'];
    $salary_range = $_POST['salary_range'];

    if (empty($title) || empty($description)) {
        $error = 'Název a popis pozice jsou povinné údaje.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE jobs SET 
                title = ?, description = ?, skills = ?, seniority = ?, 
                location = ?, collaboration_type = ?, languages = ?, 
                priority = ?, salary_range = ? 
                WHERE id = ? AND company_id = ?");
            
            $stmt->execute([
                $title, $description, $skills, $seniority, 
                $location, $collaboration_type, $languages, 
                $priority, $salary_range, $jobId, $companyId
            ]);

            // Update matches for this job
            require_once __DIR__ . '/../src/MatchingService.php';
            $matcher = new \App\MatchingService($pdo);
            $matcher->updateAllMatchesForJob($jobId);

            $_SESSION['flash_success'] = 'Pracovní pozice byla úspěšně aktualizována.';
            redirect('/dashboard.php');
        } catch (Exception $e) {
            $error = 'Chyba při ukládání: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="mb-4 fw-bold text-primary">Upravit pracovní pozici</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger small"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Název pozice</label>
                    <input type="text" name="title" class="form-control rounded-pill" value="<?= e($job['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Popis pozice</label>
                    <textarea name="description" class="form-control" rows="5" style="border-radius: 15px;" required><?= e($job['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Lokalita</label>
                        <input type="text" name="location" class="form-control rounded-pill" value="<?= e($job['location']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Seniorita</label>
                        <select name="seniority" class="form-select rounded-pill">
                            <option value="junior" <?= $job['seniority'] == 'junior' ? 'selected' : '' ?>>Junior</option>
                            <option value="mid" <?= $job['seniority'] == 'mid' ? 'selected' : '' ?>>Mid-level</option>
                            <option value="senior" <?= $job['seniority'] == 'senior' ? 'selected' : '' ?>>Senior</option>
                            <option value="expert" <?= $job['seniority'] == 'expert' ? 'selected' : '' ?>>Expert / Architect</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Typ spolupráce</label>
                        <select name="collaboration_type" class="form-select rounded-pill">
                            <option value="onsite" <?= $job['collaboration_type'] == 'onsite' ? 'selected' : '' ?>>On-site</option>
                            <option value="hybrid" <?= $job['collaboration_type'] == 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            <option value="remote" <?= $job['collaboration_type'] == 'remote' ? 'selected' : '' ?>>Remote</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Priorita zobrazení</label>
                        <select name="priority" class="form-select rounded-pill">
                            <option value="low" <?= $job['priority'] == 'low' ? 'selected' : '' ?>>Nízká</option>
                            <option value="medium" <?= $job['priority'] == 'medium' ? 'selected' : '' ?>>Střední</option>
                            <option value="high" <?= $job['priority'] == 'high' ? 'selected' : '' ?>>Vysoká (VIP)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Požadované dovednosti (oddělené čárkou)</label>
                    <input type="text" name="skills" class="form-control rounded-pill" value="<?= e($job['skills']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Jazykové požadavky</label>
                    <input type="text" name="languages" class="form-control rounded-pill" value="<?= e($job['languages']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Mzdové rozpětí / Odměna</label>
                    <input type="text" name="salary_range" class="form-control rounded-pill" value="<?= e($job['salary_range']) ?>">
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Zrušit</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Uložit změny</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
