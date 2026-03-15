<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();
requireEvent();

$eventId = getCurrentEventId();

$stmt = $pdo->prepare("SELECT m.*, cp.name as company_name, cand.first_name, cand.last_name 
                      FROM meetings m 
                      JOIN company_profiles cp ON m.company_id = cp.id 
                      JOIN candidate_profiles cand ON m.candidate_id = cand.id
                      WHERE m.event_id = ? 
                      ORDER BY m.created_at DESC");
$stmt->execute([$eventId]);
$meetings = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Přehled schůzek a výsledků</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Uchazeč</th>
                    <th>Firma</th>
                    <th>Navržený čas</th>
                    <th>Stav</th>
                    <th>Výsledek (Success)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetings as $m): ?>
                    <tr>
                        <td><strong><?= e($m['first_name'] . ' ' . $m['last_name']) ?></strong></td>
                        <td><?= e($m['company_name']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($m['suggested_at'])) ?></td>
                        <td><span class="badge rounded-pill <?= $m['status'] === 'confirmed' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= e($m['status']) ?></span></td>
                        <td>
                            <?php if ($m['outcome'] === 'hired'): ?>
                                <span class="badge bg-success rounded-pill px-3">PLÁCLI JSME SI! 🤝</span>
                            <?php elseif ($m['outcome'] === 'offer_made'): ?>
                                <span class="badge bg-primary rounded-pill px-3">Nabídka podána</span>
                            <?php elseif ($m['outcome'] === 'rejected'): ?>
                                <span class="badge bg-secondary rounded-pill px-3">Nevyšlo to</span>
                            <?php else: ?>
                                <span class="text-muted small">Probíhá...</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
