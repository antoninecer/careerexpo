<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');
requireEvent();

$eventId = getCurrentEventId();
$companyId = $_SESSION['profile_id'];
$statusFilter = $_GET['status'] ?? null;

// Handle Status Update
if (isset($_POST['update_status'])) {
    validateCsrf();
    $meetingId = (int)$_POST['meeting_id'];
    $newStatus = $_POST['new_status'];
    
    $stmt = $pdo->prepare("UPDATE meetings SET status = ? WHERE id = ? AND company_id = ?");
    $stmt->execute([$newStatus, $meetingId, $companyId]);
    $_SESSION['flash_success'] = 'Stav schůzky byl aktualizován.';
    redirect('/company_meetings.php' . ($statusFilter ? '?status=' . $statusFilter : ''));
}

$sql = "SELECT m.*, cp.first_name, cp.last_name, j.title as job_title 
        FROM meetings m 
        JOIN candidate_profiles cp ON m.candidate_id = cp.id 
        LEFT JOIN jobs j ON m.job_id = j.id 
        WHERE m.company_id = ? AND m.event_id = ?";

if ($statusFilter === 'hired') {
    $sql .= " AND m.outcome = 'hired'";
} elseif ($statusFilter === 'pending') {
    $sql .= " AND m.status = 'pending'";
}

$sql .= " ORDER BY m.suggested_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$companyId, $eventId]);
$meetings = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa schůzek</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kandidát</th>
                    <th>Pozice</th>
                    <th>Navržený čas</th>
                    <th>Stav</th>
                    <th>Výsledek</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($meetings)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Žádné schůzky k zobrazení.</td></tr>
                <?php else: ?>
                    <?php foreach ($meetings as $m): ?>
                        <tr>
                            <td><strong><?= e($m['first_name'] . ' ' . $m['last_name']) ?></strong></td>
                            <td><?= e($m['job_title'] ?: 'Obecný zájem') ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($m['suggested_at'])) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $m['status'] === 'confirmed' ? 'bg-success' : ($m['status'] === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                    <?= e($m['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($m['outcome'] === 'hired'): ?>
                                    <span class="badge bg-success rounded-pill px-3">🤝 PLÁCLI JSME SI!</span>
                                <?php elseif ($m['outcome'] !== 'pending'): ?>
                                    <span class="badge bg-light text-dark border"><?= e($m['outcome']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($m['status'] === 'pending'): ?>
                                    <form method="post" class="d-inline">
                                        <?= getCsrfInput() ?>
                                        <input type="hidden" name="meeting_id" value="<?= $m['id'] ?>">
                                        <button type="submit" name="update_status" value="confirmed" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">Potvrdit</button>
                                        <button type="submit" name="update_status" value="cancelled" class="btn btn-sm btn-outline-danger rounded-pill px-3">Zrušit</button>
                                    </form>
                                <?php endif; ?>
                                <a href="/candidate_detail.php?id=<?= $m['candidate_id'] ?>" class="btn btn-sm btn-link">Profil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
