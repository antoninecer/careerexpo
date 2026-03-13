<?php
require_once __DIR__ . '/../inc/bootstrap.php';

requireRole('candidate');

// Fetch lectures with reservations
$candidateId = $_SESSION['profile_id'];
$stmt = $pdo->prepare("SELECT l.*, 
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id) as current_occupancy,
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id AND candidate_id = ?) as is_reserved
                      FROM lectures l 
                      ORDER BY l.starts_at");
$stmt->execute([$candidateId]);
$lectures = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<h2 class="mb-4">Program přednášek a workshopů</h2>

<div class="row">
    <?php if (empty($lectures)): ?>
        <div class="col-12">
            <div class="alert alert-info">Zatím nejsou naplánovány žádné přednášky.</div>
        </div>
    <?php else: ?>
        <?php foreach ($lectures as $lecture): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h4 class="card-title fw-bold text-primary"><?= e($lecture['title']) ?></h4>
                            <span class="badge bg-info text-dark"><?= date('H:i', strtotime($lecture['starts_at'])) ?></span>
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">Přednášející: <?= e($lecture['speaker']) ?></h6>
                        <p class="card-text"><?= nl2br(e($lecture['description'])) ?></p>
                        <p class="card-text"><small class="text-muted">Lokalita: <?= e($lecture['location']) ?></small></p>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small">Kapacita: <?= $lecture['current_occupancy'] ?> / <?= $lecture['capacity'] ?></span>
                                <div class="progress" style="height: 5px; width: 100px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?= ($lecture['current_occupancy'] / $lecture['capacity']) * 100 ?>%"></div>
                                </div>
                            </div>
                            <?php if ($lecture['is_reserved']): ?>
                                <button class="btn btn-success disabled rounded-pill btn-sm">Rezervováno</button>
                            <?php elseif ($lecture['current_occupancy'] >= $lecture['capacity']): ?>
                                <button class="btn btn-danger disabled rounded-pill btn-sm">Plno</button>
                            <?php else: ?>
                                <form action="lecture_book.php" method="post">
                                    <input type="hidden" name="lecture_id" value="<?= $lecture['id'] ?>">
                                    <button type="submit" class="btn btn-primary rounded-pill btn-sm">Rezervovat místo</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

