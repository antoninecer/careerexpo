<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();
requireEvent();

$eventId = getCurrentEventId();

$stmt = $pdo->prepare("SELECT j.*, cp.name as company_name 
                      FROM jobs j 
                      JOIN company_profiles cp ON j.company_id = cp.id 
                      WHERE j.event_id = ? 
                      ORDER BY j.created_at DESC");
$stmt->execute([$eventId]);
$jobs = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Přehled pracovních pozic</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pozice</th>
                    <th>Firma</th>
                    <th>Lokalita</th>
                    <th>Seniorita</th>
                    <th>Typ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $j): ?>
                    <tr>
                        <td><strong><?= e($j['title']) ?></strong></td>
                        <td><?= e($j['company_name']) ?></td>
                        <td><?= e($j['location']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($j['seniority']) ?></span></td>
                        <td><span class="small text-muted"><?= e($j['collaboration_type']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
