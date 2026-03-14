<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');
requireEvent();

$eventId = getCurrentEventId();
$companyId = $_SESSION['profile_id'];

$stmt = $pdo->prepare("SELECT pc.*, cp.first_name, cp.last_name, cp.seniority, cp.id as candidate_profile_id 
                      FROM profile_connections pc 
                      JOIN candidate_profiles cp ON pc.candidate_id = cp.id 
                      WHERE pc.company_id = ? AND pc.event_id = ? 
                      ORDER BY pc.created_at DESC");
$stmt->execute([$companyId, $eventId]);
$connections = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Spojení z veletrhu</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kandidát</th>
                    <th>Seniorita</th>
                    <th>Datum a čas spojení</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($connections)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Zatím jste nenaskenovali žádného kandidáta.</td></tr>
                <?php else: ?>
                    <?php foreach ($connections as $c): ?>
                        <tr>
                            <td><strong><?= e($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                            <td><span class="badge bg-light text-dark border"><?= e($c['seniority']) ?></span></td>
                            <td><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="/candidate_detail.php?id=<?= $c['candidate_profile_id'] ?>" class="btn btn-sm btn-primary rounded-pill">Detail kandidáta</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
