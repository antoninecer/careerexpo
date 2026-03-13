<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

\$eventId = getCurrentEventId();
\$profileId = \$_SESSION['profile_id'];

require_once __DIR__ . '/../src/MatchingService.php';
use App\\MatchingService;

\$stmt = \$pdo->prepare(\"SELECT * FROM candidate_profiles WHERE user_id = ?\");
\$stmt->execute([\$_SESSION['user_id']]);
\$profile = \$stmt->fetch();

// Calculate and fetch matches for current EVENT
\$matcher = new MatchingService(\$pdo);
\$stmt = \$pdo->prepare(\"SELECT j.*, c.name as company_name FROM jobs j 
                      JOIN company_profiles c ON j.company_id = c.id 
                      WHERE j.event_id = ?\");
\$stmt->execute([\$eventId]);
\$jobs = \$stmt->fetchAll();

\$recommendedJobs = [];
foreach (\$jobs as \$job) {
    \$match = \$matcher->calculateMatch(\$profile['id'], \$job['id']);
    if (\$match && \$match['score'] >= 40) {
        \$job['match_score'] = \$match['score'];
        \$job['match_color'] = \$match['color'];
        \$recommendedJobs[] = \$job;
    }
}

// Sort by score
usort(\$recommendedJobs, fn(\$a, \$b) => \$b['match_score'] <=> \$a['match_score']);

include_once __DIR__ . '/../templates/header.php';
?>

<div class=\"row\">
    <div class=\"col-md-3\">
        <div class=\"card p-3 mb-4 shadow-sm border-0\">
            <h5 class=\"card-title text-primary fw-bold\">Můj Profil</h5>
            <hr>
            <p class=\"mb-1\"><strong><?= e(\$profile['first_name'] . ' ' . \$profile['last_name']) ?></strong></p>
            <p class=\"small text-muted mb-3\"><?= e(\$_SESSION['user_email']) ?></p>
            <p class=\"mb-1 small\"><strong>Lokalita:</strong> <?= e(\$profile['location'] ?: '-') ?></p>
            <p class=\"mb-1 small\"><strong>Seniorita:</strong> <span class=\"badge bg-secondary\"><?= e(\$profile['seniority'] ?: '-') ?></span></p>
            
            <div class=\"mt-3\">
                <?php
                \$stmt = \$pdo->prepare(\"SELECT id FROM candidate_files WHERE candidate_id = ?\");
                \$stmt->execute([\$profile['id']]);
                \$hasCv = \$stmt->fetch();
                ?>
                <p class=\"mb-0 small\">
                    <strong>CV:</strong> 
                    <?php if (\$hasCv): ?>
                        <span class=\"text-success fw-bold\"><i class=\"bi bi-check-circle-fill\"></i> nahráno</span>
                    <?php else: ?>
                        <span class=\"text-danger fw-bold\"><i class=\"bi bi-x-circle-fill\"></i> chybí</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class=\"mt-4 p-3 bg-light rounded text-center\">
                <p class=\"small mb-2 fw-bold\">Párovací kód:</p>
                <h3 class=\"text-primary fw-bold mb-3\"><?= e(\$profile['pairing_code']) ?></h3>
                <img src=\"https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode(\$profile['pairing_code']) ?>\" alt=\"QR kód\" class=\"img-fluid mb-2 rounded shadow-sm\">
                <p class=\"small text-muted mb-0\">Ukažte tento kód firmě u stánku.</p>
            </div>

            <a href=\"/profile_edit.php\" class=\"btn btn-sm btn-primary w-100 rounded-pill mt-4\">Upravit profil</a>
        </div>
    </div>

    <div class=\"col-md-9\">
        <div class=\"row g-3 mb-4\">
            <div class=\"col-md-4\">
                <a href=\"#recommended-jobs\" class=\"text-decoration-none\">
                    <div class=\"card p-3 text-center bg-white shadow-sm h-100 border-0\">
                        <h2 class=\"text-primary fw-bold mb-0\"><?= count(\$recommendedJobs) ?></h2>
                        <p class=\"small mb-0 text-muted\">Doporučené firmy</p>
                    </div>
                </a>
            </div>
            <div class=\"col-md-4\">
                <a href=\"/meetings.php\" class=\"text-decoration-none\">
                    <div class=\"card p-3 text-center bg-white shadow-sm h-100 border-0\">
                        <?php
                        \$stmt = \$pdo->prepare(\"SELECT COUNT(*) FROM meetings WHERE candidate_id = ? AND event_id = ?\");
                        \$stmt->execute([\$profile['id'], \$eventId]);
                        \$meetingCount = \$stmt->fetchColumn();
                        ?>
                        <h2 class=\"text-success fw-bold mb-0\"><?= (int)\$meetingCount ?></h2>
                        <p class=\"small mb-0 text-muted\">Moje schůzky</p>
                    </div>
                </a>
            </div>
            <div class=\"col-md-4\">
                <a href=\"/lectures.php\" class=\"text-decoration-none\">
                    <div class=\"card p-3 text-center bg-white shadow-sm h-100 border-0\">
                        <?php
                        \$stmt = \$pdo->prepare(\"SELECT COUNT(*) FROM lecture_reservations lr JOIN lectures l ON lr.lecture_id = l.id WHERE lr.candidate_id = ? AND l.event_id = ?\");
                        \$stmt->execute([\$profile['id'], \$eventId]);
                        \$lectureCount = \$stmt->fetchColumn();
                        ?>
                        <h2 class=\"text-info fw-bold mb-0\"><?= (int)\$lectureCount ?></h2>
                        <p class=\"small mb-0 text-muted\">Rezervace přednášek</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="card p-4 shadow-sm border-0" id="recommended-jobs">
            <h4 class="mb-4 fw-bold">Doporučené firmy a pozice</h4>
            
            <?php if (empty($recommendedJobs)): ?>
                <div class="alert alert-info">Doplňte své dovednosti pro získání doporučení.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($recommendedJobs as $job): ?>
                        <div class="col-12">
                            <div class="card border p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1 text-primary fw-bold"><?= e($job['title']) ?></h5>
                                        <p class="mb-0 small text-muted"><strong><?= e($job['company_name']) ?></strong> | <?= e($job['location']) ?></p>
                                    </div>
                                    <span class="badge badge-<?= $job['match_color'] ?> rounded-pill px-3 py-2">Shoda <?= $job['match_score'] ?>%</span>
                                </div>
                                <div class="mt-3 text-end">
                                    <a href="/company_detail.php?id=<?= $job['company_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Detail firmy</a>
                                    <a href="meeting_request.php?job_id=<?= $job['id'] ?>" class="btn btn-sm btn-success rounded-pill ms-2">Schůzka</a>
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
include_once __DIR__ . '/../templates/footer.php';
?>
