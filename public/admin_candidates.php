<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();

// Handle Deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT user_id FROM candidate_profiles WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $userId = $stmt->fetchColumn();
    if ($userId) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
        $_SESSION['flash_success'] = 'Uchazeč byl smazán.';
    }
    redirect('/admin_candidates.php');
}

$stmt = $pdo->query("SELECT cp.*, u.email, 
                    (SELECT id FROM candidate_files WHERE candidate_id = cp.id LIMIT 1) as cv_id 
                    FROM candidate_profiles cp 
                    JOIN users u ON cp.user_id = u.id 
                    ORDER BY cp.created_at DESC");
$candidates = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa uchazečů</h2>
    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm">Zpět</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jméno</th>
                    <th>Email</th>
                    <th>Lokalita</th>
                    <th>Seniorita</th>
                    <th>CV</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $c): ?>
                    <tr>
                        <td><strong><?= e($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
                        <td><?= e($c['email']) ?></td>
                        <td><?= e($c['location']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($c['seniority']) ?></span></td>
                        <td>
                            <?php if ($c['cv_id']): ?>
                                <span class="text-success"><i class="bi bi-file-earmark-check-fill"></i> Ano</span>
                            <?php else: ?>
                                <span class="text-muted small">Ne</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="/admin_candidates.php?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Smazat uchazeče?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
