<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');
requireEvent();

$eventId = getCurrentEventId();
$candidateId = (int)($_GET['id'] ?? 0);
$companyId = $_SESSION['profile_id'];

if (!$candidateId) {
    redirect('/dashboard.php');
}

// Fetch candidate profile with contact email
$stmt = $pdo->prepare("SELECT cp.*, u.email 
                      FROM candidate_profiles cp 
                      JOIN users u ON cp.user_id = u.id 
                      WHERE cp.id = ?");
$stmt->execute([$candidateId]);
$candidate = $stmt->fetch();

if (!$candidate) {
    $_SESSION['flash_error'] = 'Profil kandidáta nebyl nalezen.';
    redirect('/dashboard.php');
}

// Fetch connection status
$stmt = $pdo->prepare("SELECT * FROM profile_connections WHERE candidate_id = ? AND company_id = ? AND event_id = ?");
$stmt->execute([$candidateId, $companyId, $eventId]);
$connection = $stmt->fetch();

// Fetch CV
$stmt = $pdo->prepare("SELECT * FROM candidate_files WHERE candidate_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$candidateId]);
$cv = $stmt->fetch();

// Calculate matching score for this candidate against all company jobs in this event
require_once __DIR__ . '/../src/MatchingService.php';
$matcher = new \App\MatchingService($pdo);
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? AND event_id = ?");
$stmt->execute([$companyId, $eventId]);
$companyJobs = $stmt->fetchAll();

$bestScore = 0;
foreach ($companyJobs as $job) {
    $m = $matcher->calculateMatch($candidateId, $job['id']);
    if ($m && $m['score'] > $bestScore) $bestScore = $m['score'];
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold text-primary mb-0"><?= e($candidate['first_name'] . ' ' . $candidate['last_name']) ?></h1>
                <span class="badge bg-secondary rounded-pill px-3"><?= e($candidate['seniority']) ?></span>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Lokalita</p>
                    <p class="fw-bold"><?= e($candidate['location'] ?: 'Neuvedeno') ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Preferovaná spolupráce</p>
                    <p class="fw-bold text-capitalize"><?= e($candidate['preferred_collaboration']) ?></p>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Dovednosti</h5>
            <div class="mb-4">
                <?php if ($candidate['skills']): ?>
                    <?php foreach (explode(',', $candidate['skills']) as $skill): ?>
                        <span class="badge bg-light text-dark border p-2 mb-1"><?= e(trim($skill)) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">Žádné dovednosti neuvedeny.</p>
                <?php endif; ?>
            </div>

            <h5 class="fw-bold mb-3">O mně / Bio</h5>
            <p class="text-secondary mb-4"><?= nl2br(e($candidate['bio'])) ?></p>

            <div class="row mt-5">
                <div class="col-md-4">
                    <a href="<?= e($candidate['linkedin_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-pill mb-2 <?= !$candidate['linkedin_url'] ? 'disabled' : '' ?>">
                        <i class="bi bi-linkedin me-2"></i>LinkedIn
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?= e($candidate['github_url']) ?>" target="_blank" class="btn btn-sm btn-outline-dark w-100 rounded-pill mb-2 <?= !$candidate['github_url'] ? 'disabled' : '' ?>">
                        <i class="bi bi-github me-2"></i>GitHub
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="<?= e($candidate['portfolio_url']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill mb-2 <?= !$candidate['portfolio_url'] ? 'disabled' : '' ?>">
                        <i class="bi bi-person-badge me-2"></i>Portfolio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 mb-4 bg-light">
            <h5 class="fw-bold mb-4">Akce a Propojení</h5>
            
            <div class="text-center mb-4">
                <div class="display-4 fw-bold text-success"><?= $bestScore ?>%</div>
                <div class="small text-muted text-uppercase">Nejvyšší Matching Skóre</div>
            </div>

            <hr>

            <?php if ($cv): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Životopis kandidáta</label>
                    <a href="/cv_view.php?id=<?= $cv['id'] ?>" target="_blank" class="btn btn-primary w-100 rounded-pill shadow-sm">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Otevřít CV
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning small border-0 shadow-sm"><i class="bi bi-exclamation-triangle me-2"></i>Kandidát zatím nenahrál CV.</div>
            <?php endif; ?>

            <form action="/meeting_feedback.php" method="post" class="mt-4">
                <?= getCsrfInput() ?>
                <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                <label class="form-label fw-bold small text-muted text-uppercase">Stav zájmu</label>
                <div class="d-grid gap-2">
                    <button type="submit" name="status" value="yes" class="btn btn-success rounded-pill btn-sm">Máme velký zájem</button>
                    <button type="submit" name="status" value="maybe" class="btn btn-outline-secondary rounded-pill btn-sm">Možná později</button>
                    <button type="submit" name="status" value="no" class="btn btn-outline-danger rounded-pill btn-sm">Nemáme zájem</button>
                </div>
            </form>
        </div>

        <div class="card p-4 shadow-sm border-0">
            <h6 class="fw-bold mb-3 small">Kontaktní údaje</h6>
            <p class="mb-1 small"><i class="bi bi-envelope me-2"></i><?= e($candidate['email']) ?></p>
            <p class="mb-0 small"><i class="bi bi-phone me-2"></i><?= e($candidate['phone'] ?: 'Není k dispozici') ?></p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
