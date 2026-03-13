<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');

$candidateId = $_SESSION['profile_id'];
$stmt = $pdo->prepare("SELECT l.*, 
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id) as current_occupancy,
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id AND candidate_id = ?) as is_reserved
                      FROM lectures l 
                      ORDER BY l.starts_at, l.location");
$stmt->execute([$candidateId]);
$lectures = $stmt->fetchAll();

$grouped = [];
foreach ($lectures as $l) {
    $time = date('H:i', strtotime($l['starts_at']));
    $grouped[$time][] = $l;
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-primary mb-0">Program veletrhu</h2>
        <p class="text-muted mb-0">Naplánujte si svůj den mezi sály a workshopy.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <span class="badge bg-dark rounded-pill px-3 py-2">Pátek 13. března</span>
    </div>
</div>

<?php if (empty($grouped)): ?>
    <div class="alert alert-info">Zatím nejsou naplánovány žádné přednášky.</div>
<?php else: ?>
    <div class="program-grid">
        <?php foreach ($grouped as $time => $sessions): ?>
            <div class="row mb-5 border-bottom pb-4">
                <div class="col-lg-1 col-md-2 mb-3 mb-md-0">
                    <div class="h3 fw-bold text-primary"><?= $time ?></div>
                </div>
                <div class="col-lg-11 col-md-10">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        <?php foreach ($sessions as $lecture): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm <?= $lecture['is_reserved'] ? 'border-success border-2' : 'border-0' ?>">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="badge bg-info text-dark rounded-pill px-3"><?= e($lecture['location']) ?></span>
                                            <?php if ($lecture['is_reserved']): ?>
                                                <span class="badge bg-success text-white rounded-pill px-3">Rezervováno</span>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="card-title fw-bold mb-1"><?= e($lecture['title']) ?></h5>
                                        <p class="text-muted small mb-3">Přednášející: <strong><?= e($lecture['speaker']) ?></strong></p>
                                        <p class="card-text small mb-4 flex-grow-1 text-secondary"><?= nl2br(e($lecture['description'])) ?></p>
                                        
                                        <div class="mt-auto pt-3 border-top">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Kapacita: <?= $lecture['current_occupancy'] ?> / <?= $lecture['capacity'] ?></small>
                                                <small class="text-muted"><?= round(($lecture['capacity'] > 0 ? $lecture['current_occupancy']/$lecture['capacity'] : 0) * 100) ?>%</small>
                                            </div>
                                            <div class="progress mb-3" style="height: 5px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($lecture['capacity'] > 0 ? $lecture['current_occupancy']/$lecture['capacity'] : 0) * 100 ?>%"></div>
                                            </div>

                                            <?php if ($lecture['is_reserved']): ?>
                                                <form action="lecture_cancel.php" method="post" onsubmit="return confirm('Opravdu chcete zrušit rezervaci této přednášky?');">
                                                    <?= getCsrfInput() ?>
                                                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Zrušit rezervaci</button>
                                                </form>
                                            <?php elseif ($lecture['current_occupancy'] >= $lecture['capacity']): ?>
                                                <button class="btn btn-secondary w-100 rounded-pill btn-sm disabled">Plno</button>
                                            <?php else: ?>
                                                <form action="lecture_book.php" method="post">
                                                    <?= getCsrfInput() ?>
                                                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                                                    <button type="submit" class="btn btn-primary w-100 rounded-pill btn-sm">Rezervovat místo</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.card { transition: transform 0.2s; }
.card:hover { transform: translateY(-3px); }
</style>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
