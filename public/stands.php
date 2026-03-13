<?php
require_once __DIR__ . '/../inc/bootstrap.php';

requireRole('candidate');

// Fetch stands with companies
$stmt = $pdo->query("SELECT s.*, cp.name as company_name, cp.id as company_id 
                    FROM stands s 
                    LEFT JOIN company_profiles cp ON s.id = cp.stand_id 
                    ORDER BY s.zone, s.name");
$stands = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<h2 class="mb-4">Mapa stánků a vystavovatelé</h2>

<div class="row">
    <?php if (empty($stands)): ?>
        <div class="col-12">
            <div class="alert alert-info">Zatím nebyly definovány žádné stánky.</div>
        </div>
    <?php else: ?>
        <?php foreach ($stands as $stand): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title fw-bold"><?= e($stand['name']) ?></h5>
                            <span class="badge bg-info text-dark"><?= e($stand['zone']) ?></span>
                        </div>
                        <p class="card-text text-muted mb-2"><?= e($stand['location']) ?></p>
                        <hr>
                        <?php if ($stand['company_id']): ?>
                            <p class="mb-0"><strong>Firma:</strong> <?= e($stand['company_name']) ?></p>
                            <a href="company_detail.php?id=<?= $stand['company_id'] ?>" class="btn btn-sm btn-outline-primary mt-3 rounded-pill">Zobrazit firmu</a>
                        <?php else: ?>
                            <p class="mb-0 text-muted italic">Volný stánek</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="mt-5 p-4 bg-white rounded shadow-sm">
    <h4>Doporučená trasa</h4>
    <p class="text-muted">Na základě vašich preferencí a matchingu doporučujeme navštívit tyto stánky:</p>
    <div class="alert alert-secondary">
        Brzy dostupné: Automatické generování trasy podle nejvyšší shody.
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

