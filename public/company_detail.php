<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

$eventId = getCurrentEventId();
$companyId = (int)($_GET['id'] ?? 0);

if (!$companyId) {
    redirect('/dashboard.php');
}

// Fetch company profile with stand info
$stmt = $pdo->prepare("SELECT cp.*, s.name as stand_name, s.zone as stand_zone, s.location as stand_location 
                      FROM company_profiles cp 
                      LEFT JOIN stands s ON cp.stand_id = s.id 
                      WHERE cp.id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

if (!$company) {
    $_SESSION['flash_error'] = 'Firma nebyla nalezena.';
    redirect('/dashboard.php');
}

// Fetch jobs for this company in current event
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? AND event_id = ? ORDER BY created_at DESC");
$stmt->execute([$companyId, $eventId]);
$jobs = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold text-primary mb-0"><?= e($company['name']) ?></h1>
                <?php if ($company['website']): ?>
                    <a href="<?= e($company['website']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-globe me-2"></i>Webové stránky
                    </a>
                <?php endif; ?>
            </div>

            <p class="lead text-muted mb-4"><?= nl2br(e($company['description'])) ?></p>

            <?php if ($company['video_url']): ?>
                <div class="mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-play-btn me-2"></i>Představení firmy</h5>
                    <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                        <iframe src="<?= e($company['video_url']) ?>" title="Company Video" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endif; ?>

            <h4 class="fw-bold mt-5 mb-4">Otevřené pozice na tomto veletrhu</h4>
            <?php if (empty($jobs)): ?>
                <div class="alert alert-light border small text-muted">Tato firma aktuálně nemá vypsané žádné pozice pro tuto akci.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-12">
                            <div class="card border p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold mb-1 small text-dark"><?= e($job['title']) ?></h5>
                                        <p class="mb-0 small text-muted"><?= e($job['location']) ?> | <?= e($job['seniority']) ?></p>
                                    </div>
                                    <a href="/meeting_request.php?job_id=<?= $job['id'] ?>" class="btn btn-sm btn-success text-white rounded-pill px-3 shadow-sm">
                                        Požádat o schůzku
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 bg-dark text-white mb-4">
            <h5 class="fw-bold mb-4">Kde nás najdete?</h5>
            <?php if ($company['type'] === 'physical' && $company['stand_name']): ?>
                <div class="mb-3">
                    <div class="small text-muted text-uppercase fw-bold">Stánek</div>
                    <div class="h4 fw-bold mb-0 text-info"><?= e($company['stand_name']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted text-uppercase fw-bold">Lokalita</div>
                    <div class="fw-bold small"><?= e($company['stand_location']) ?> (<?= e($company['stand_zone']) ?>)</div>
                </div>
            <?php else: ?>
                <div class="alert alert-info bg-opacity-10 border-info text-info small mb-0">
                    <i class="bi bi-broadcast me-2"></i>Tato firma je přítomna <strong>virtuálně</strong>.
                </div>
            <?php endif; ?>

            <?php if ($company['meeting_url']): ?>
                <hr class="opacity-25 my-4">
                <a href="<?= e($company['meeting_url']) ?>" target="_blank" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold">
                    <i class="bi bi-camera-video me-2"></i>Vstoupit do virtuální místnosti
                </a>
                <p class="text-center small mt-2 text-muted mb-0">Meeting probíhá přes externí platformu.</p>
            <?php endif; ?>

            <?php if ($company['brochure_url']): ?>
                <hr class="opacity-25 my-4">
                <a href="<?= e($company['brochure_url']) ?>" target="_blank" class="btn btn-outline-info w-100 rounded-pill">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Stáhnout brožuru
                </a>
            <?php endif; ?>
        </div>

        <a href="/dashboard.php" class="btn btn-link text-muted w-100"><i class="bi bi-arrow-left me-2"></i>Zpět na dashboard</a>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
