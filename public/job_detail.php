<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

$jobId = (int)($_GET['id'] ?? 0);
if (!$jobId) {
    redirect('/dashboard.php');
}

// Fetch job with company profile
$stmt = $pdo->prepare("SELECT j.*, c.name as company_name, c.description as company_desc, c.website, c.type as company_type, s.name as stand_name, s.zone as stand_zone
                      FROM jobs j 
                      JOIN company_profiles c ON j.company_id = c.id 
                      LEFT JOIN stands s ON c.stand_id = s.id
                      WHERE j.id = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    $_SESSION['flash_error'] = 'Pozice nebyla nalezena.';
    redirect('/dashboard.php');
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm mb-4 border-0">
            <h1 class="text-primary fw-bold mb-3"><?= e($job['title']) ?></h1>
            <p class="lead text-muted mb-4"><?= nl2br(e($job['description'])) ?></p>
            
            <h5 class="fw-bold mt-4">Požadované dovednosti</h5>
            <div class="mb-4">
                <?php if ($job['skills']): ?>
                    <?php foreach (explode(',', $job['skills']) as $skill): ?>
                        <span class="badge bg-light text-dark border p-2"><?= e(trim($skill)) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="row bg-light p-3 rounded">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Lokalita:</strong> <?= e($job['location'] ?: 'Neuvedeno') ?></p>
                    <p class="mb-1"><strong>Seniorita:</strong> <?= e($job['seniority'] ?: 'Neuvedeno') ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Typ:</strong> <?= e($job['collaboration_type'] ?: 'Neuvedeno') ?></p>
                    <p class="mb-1"><strong>Plat:</strong> <?= e($job['salary_range'] ?: 'Dle dohody') ?></p>
                </div>
            </div>
            
            <div class="mt-5">
                <a href="meeting_request.php?job_id=<?= $job['id'] ?>" class="btn btn-primary btn-lg rounded-pill px-5">Chci se potkat!</a>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4 ms-2">Zpět</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 bg-dark text-white">
            <h4 class="fw-bold mb-3"><?= e($job['company_name']) ?></h4>
            <p class="small mb-4"><?= nl2br(e($job['company_desc'])) ?></p>
            <hr class="bg-white opacity-25">
            <p class="mb-2"><strong>Web:</strong> <a href="<?= e($job['website']) ?>" class="text-white" target="_blank"><?= e($job['website']) ?></a></p>
            <p class="mb-2"><strong>Účast:</strong> <?= $job['company_type'] === 'physical' ? 'Fyzicky' : 'Virtuálně' ?></p>
            <?php if ($job['stand_name']): ?>
                <p class="mb-2"><strong>Stánek:</strong> <?= e($job['stand_name']) ?> (Zóna: <?= e($job['stand_zone']) ?>)</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/header.php';
?>

