<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('candidate');
requireEvent();

$eventId = getCurrentEventId();

// Fetch stands with companies for current event
$stmt = $pdo->prepare("SELECT s.*, cp.name as company_name, cp.id as company_id 
                    FROM stands s 
                    LEFT JOIN company_profiles cp ON s.id = cp.stand_id 
                    WHERE s.event_id = ?
                    ORDER BY s.zone, s.name");
$stmt->execute([$eventId]);
$stands = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<h2 class="mb-4 fw-bold text-primary">Mapa stánků a vystavovatelé</h2>

<div class="row">
    <?php if (empty($stands)): ?>
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">Pro tuto akci zatím nebyly definovány žádné stánky.</div>
        </div>
    <?php else: ?>
        <?php foreach ($stands as $stand): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold mb-0"><?= e($stand['name']) ?></h5>
                            <span class="badge bg-info text-dark rounded-pill"><?= e($stand['zone']) ?></span>
                        </div>
                        <p class="card-text text-muted small mb-3"><i class="bi bi-geo-alt"></i> <?= e($stand['location']) ?></p>
                        <hr class="opacity-10">
                        <?php if ($stand['company_id']): ?>
                            <p class="mb-1 small"><strong>Firma:</strong></p>
                            <h6 class="fw-bold mb-3"><?= e($stand['company_name']) ?></h6>
                            <a href="/company_detail.php?id=<?= $stand['company_id'] ?>" class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm">Zobrazit firmu</a>
                        <?php else: ?>
                            <p class="mb-0 text-muted italic small">Volný stánek</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="mt-5 p-4 bg-white rounded shadow-sm border-start border-5 border-primary">
    <h4 class="fw-bold">Doporučená trasa</h4>
    <p class="text-muted small">Na základě vašich preferencí a matchingu doporučujeme navštívit tyto stánky jako první.</p>
    <div class="alert alert-secondary border-0 mb-0 small">
        <i class="bi bi-info-circle me-2"></i>Brzy dostupné: Automatické generování trasy podle nejvyšší shody.
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
