<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

$eventId = getCurrentEventId();
$candidateId = $_SESSION['profile_id'];

// Fetch all meetings for this candidate in current event
$stmt = $pdo->prepare("SELECT m.*, cp.name as company_name, j.title as job_title 
                      FROM meetings m
                      JOIN company_profiles cp ON m.company_id = cp.id
                      LEFT JOIN jobs j ON m.job_id = j.id
                      WHERE m.candidate_id = ? AND m.event_id = ?
                      ORDER BY m.suggested_at DESC");
$stmt->execute([$candidateId, $eventId]);
$meetings = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Moje schůzky</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět na dashboard</a>
</div>

<?php if (empty($meetings)): ?>
    <div class="card shadow-sm border-0 p-5 text-center">
        <p class="text-muted">Zatím jste pro tuto akci nepožádal o žádnou schůzku.</p>
        <a href="/dashboard.php#recommended-jobs" class="btn btn-primary rounded-pill">Prohlédnout doporučené firmy</a>
    </div>
<?php else: ?>
    <?php foreach ($meetings as $m): ?>
        <div class="card shadow-sm border-0 mb-3 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-8 p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1"><?= e($m['company_name']) ?></h5>
                            <p class="text-muted mb-3 small"><?= e($m['job_title'] ?: 'Obecná schůzka') ?></p>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 <?= $m['status'] === 'confirmed' ? 'bg-success' : ($m['status'] === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                            <?php 
                                if ($m['status'] === 'confirmed') echo 'Potvrzeno';
                                elseif ($m['status'] === 'cancelled') echo 'Zrušeno';
                                else echo 'Čeká na potvrzení';
                            ?>
                        </span>
                    </div>
                    <div class="small">
                        <p class="mb-1"><strong><i class="bi bi-clock me-2"></i>Navržený čas:</strong> <?= date('d.m.Y H:i', strtotime($m['suggested_at'])) ?></p>
                        <p class="mb-0 text-muted italic"><strong>Moje poznámka:</strong> <?= e($m['notes']) ?></p>
                    </div>
                </div>
                <div class="col-md-4 bg-light p-4 border-start d-flex flex-column justify-content-center">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Výsledek schůzky</h6>
                    <?php if ($m['outcome'] === 'pending'): ?>
                        <form action="/meeting_feedback.php" method="post" class="d-grid gap-2">
                            <?= getCsrfInput() ?>
                            <input type="hidden" name="meeting_id" value="<?= $m['id'] ?>">
                            <button type="submit" name="outcome" value="offer_made" class="btn btn-outline-primary btn-sm rounded-pill">Dostal jsem nabídku</button>
                            <button type="submit" name="outcome" value="hired" class="btn btn-success btn-sm rounded-pill text-white fw-bold shadow-sm">PLÁCLI JSME SI! 🤝</button>
                            <button type="submit" name="outcome" value="rejected" class="btn btn-outline-danger btn-sm rounded-pill">Nevyšlo to</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-2">
                            <?php if ($m['outcome'] === 'hired'): ?>
                                <div class="h2 mb-1">🤝</div>
                                <span class="badge bg-success rounded-pill px-3">ÚSPĚCH! NASTOUPENO</span>
                            <?php elseif ($m['outcome'] === 'offer_made'): ?>
                                <div class="h2 mb-1">📩</div>
                                <span class="badge bg-primary rounded-pill px-3">DOSTÁNA NABÍDKA</span>
                            <?php elseif ($m['outcome'] === 'rejected'): ?>
                                <div class="h2 mb-1">❌</div>
                                <span class="badge bg-secondary rounded-pill px-3">NEVYŠLO TO</span>
                            <?php endif; ?>
                            <div class="mt-3">
                                <form action="/meeting_feedback.php" method="post">
                                    <?= getCsrfInput() ?>
                                    <input type="hidden" name="meeting_id" value="<?= $m['id'] ?>">
                                    <button type="submit" name="outcome" value="pending" class="btn btn-link btn-sm text-muted">Změnit výsledek</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
