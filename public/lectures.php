<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

$eventId = getCurrentEventId();
$candidateId = $_SESSION['profile_id'];

// Fetch lectures for current event with occupancy and user reservation status
$stmt = $pdo->prepare("SELECT l.*, 
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id) as current_occupancy,
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id AND candidate_id = ?) as is_reserved
                      FROM lectures l 
                      WHERE l.event_id = ?
                      ORDER BY l.starts_at, l.location");
$stmt->execute([$candidateId, $eventId]);
$lectures = $stmt->fetchAll();

// Group by time for the timeline view
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
        <p class="text-muted mb-0 small">Naplánujte si svůj den mezi sály a workshopy.</p>
    </div>
    <div class="text-end d-none d-md-block">
        <span class="badge bg-dark rounded-pill px-3 py-2 small">Pátek 13. března</span>
    </div>
</div>

<?php if (empty($grouped)): ?>
    <div class="alert alert-info border-0 shadow-sm">Pro tuto akci zatím nejsou naplánovány žádné přednášky.</div>
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
                                            <div class="d-flex gap-1">
                                                <span class="badge bg-info text-dark rounded-pill px-3"><?= e($lecture['location']) ?></span>
                                                <?php if ($lecture['is_virtual']): ?>
                                                    <span class="badge bg-primary text-white rounded-pill px-3"><i class="bi bi-broadcast"></i> ONLINE</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($lecture['is_reserved']): ?>
                                                <span class="badge bg-success text-white rounded-pill px-3">Rezervováno</span>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="card-title fw-bold mb-1"><?= e($lecture['title']) ?></h5>
                                        <p class="text-muted small mb-3">Přednášející: <strong><?= e($lecture['speaker']) ?></strong></p>
                                        <p class="card-text small mb-4 flex-grow-1 text-secondary"><?= nl2br(e($lecture['description'])) ?></p>
                                        
                                        <div class="mt-auto pt-3 border-top">
                                            <?php if ($lecture['is_reserved'] && $lecture['is_virtual'] && $lecture['stream_url']): ?>
                                                <a href="<?= e($lecture['stream_url']) ?>" target="_blank" class="btn btn-primary btn-sm w-100 rounded-pill mb-3 shadow">
                                                    <i class="bi bi-play-circle me-2"></i> Sledovat stream
                                                </a>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Kapacita: <?= $lecture['current_occupancy'] ?> / <?= $lecture['capacity'] ?></small>
                                                <small class="text-muted"><?= round(($lecture['capacity'] > 0 ? $lecture['current_occupancy']/$lecture['capacity'] : 0) * 100) ?>%</small>
                                            </div>
                                            <div class="progress mb-3" style="height: 5px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($lecture['capacity'] > 0 ? $lecture['current_occupancy']/$lecture['capacity'] : 0) * 100 ?>%"></div>
                                            </div>

                                            <?php if ($lecture['is_reserved']): ?>
                                                <form action="/lecture_cancel.php" method="post" onsubmit="return confirm('Opravdu chcete zrušit rezervaci této přednášky?');">
                                                    <?= getCsrfInput() ?>
                                                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Zrušit rezervaci</button>
                                                </form>
                                            <?php elseif ($lecture['current_occupancy'] >= $lecture['capacity']): ?>
                                                <button class="btn btn-secondary w-100 rounded-pill btn-sm disabled">Plno</button>
                                            <?php else: ?>
                                                <form action="/lecture_book.php" method="post">
                                                    <?= getCsrfInput() ?>
                                                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                                                    <button type="submit" class="btn btn-primary w-100 rounded-pill btn-sm shadow-sm">Rezervovat místo</button>
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

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
