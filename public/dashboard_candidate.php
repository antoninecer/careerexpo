<?php
// Included in public/dashboard.php
// role: candidate

require_once __DIR__ . '/../src/MatchingService.php';
use App\MatchingService;

$stmt = $pdo->prepare("SELECT * FROM candidate_profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

// Calculate and fetch matches
$matcher = new MatchingService($pdo);
$stmt = $pdo->query("SELECT j.*, c.name as company_name FROM jobs j JOIN company_profiles c ON j.company_id = c.id");
$jobs = $stmt->fetchAll();

$recommendedJobs = [];
foreach ($jobs as $job) {
    $match = $matcher->calculateMatch($profile['id'], $job['id']);
    if ($match['score'] >= 40) {
        $job['match_score'] = $match['score'];
        $job['match_color'] = $match['color'];
        $recommendedJobs[] = $job;
    }
}

// Sort by score
usort($recommendedJobs, fn($a, $b) => $b['match_score'] <=> $a['match_score']);

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 mb-4 shadow-sm">
            <h5 class="card-title text-primary fw-bold">Můj Profil</h5>
            <hr>
            <p class="mb-1"><strong><?= e($profile['first_name'] . ' ' . $profile['last_name']) ?></strong></p>
            <p class="mb-1"><strong>Email:</strong> <?= e($_SESSION['user_email']) ?></p>
            <p class="mb-1"><strong>Lokalita:</strong> <?= e($profile['location'] ?: '-') ?></p>
            <p class="mb-1"><strong>Seniorita:</strong> <span class="badge bg-secondary"><?= e($profile['seniority'] ?: '-') ?></span></p>

            <div class="mt-4 p-3 bg-light rounded text-center">
                <p class="small mb-2 fw-bold">Můj párovací kód:</p>
                <h3 class="text-primary fw-bold mb-3"><?= e($profile['pairing_code']) ?></h3>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($profile['pairing_code']) ?>" alt="QR kód" class="img-fluid mb-2">
                <p class="small text-muted mb-0">Ukažte tento kód firmě u stánku pro rychlé spojení.</p>
            </div>

            <div class="mt-4">
                <strong>Dovednosti:</strong><br>
                <?php if ($profile['skills']): ?>
                    <?php foreach (explode(',', $profile['skills']) as $skill): ?>
                        <span class="badge bg-light text-dark border mt-1"><?= e(trim($skill)) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="small text-muted">Nevyplněno</span>
                <?php endif; ?>
            </div>

            <a href="profile_edit.php" class="btn btn-sm btn-primary w-100 rounded-pill mt-4">Upravit profil / Nahrát CV</a>
        </div>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                    <h2 class="text-primary fw-bold"><?= count($recommendedJobs) ?></h2>
                    <p class="mb-0 text-muted">Doporučené firmy</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                    <h2 class="text-success fw-bold">0</h2>
                    <p class="mb-0 text-muted">Potvrzené schůzky</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                    <h2 class="text-info fw-bold">0</h2>
                    <p class="mb-0 text-muted">Rezervace přednášek</p>
                </div>
            </div>
        </div>

        <div class="card p-4 shadow-sm border-0">
            <h4 class="mb-4 fw-bold">Doporučené firmy a pozice pro vás</h4>
            
            <?php if (empty($recommendedJobs)): ?>
                <div class="alert alert-info">Doplňte své dovednosti a seniority v profilu pro získání lepších doporučení.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($recommendedJobs as $job): ?>
                        <div class="col-md-12 mb-3">
                            <div class="card border p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 text-primary fw-bold"><?= e($job['title']) ?></h5>
                                        <p class="mb-0"><strong><?= e($job['company_name']) ?></strong> | <?= e($job['location']) ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-<?= $job['match_color'] ?> fs-6 rounded-pill px-3 py-2">
                                            Shoda: <?= $job['match_score'] ?>%
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <?php if ($job['skills']): ?>
                                        <?php foreach (explode(',', $job['skills']) as $skill): ?>
                                            <span class="badge bg-light text-dark border"><?= e(trim($skill)) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-3 text-end">
                                    <a href="job_detail.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Zobrazit detail</a>
                                    <a href="meeting_request.php?job_id=<?= $job['id'] ?>" class="btn btn-sm btn-success rounded-pill">Požádat o schůzku</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/header.php';
?>

