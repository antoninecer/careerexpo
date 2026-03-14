<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('admin');

// Handle Deletion
if (isset($_GET['delete'])) {
    $companyId = (int)$_GET['delete'];
    
    // Get user_id first to delete the account too
    $stmt = $pdo->prepare("SELECT user_id FROM company_profiles WHERE id = ?");
    $stmt->execute([$companyId]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
        $pdo->beginTransaction();
        try {
            // Foreign keys handle profile and jobs deletion via ON DELETE CASCADE
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
            $pdo->commit();
            $_SESSION['flash_success'] = 'Firma i její uživatelský účet byly smazány.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = 'Chyba při mazání: ' . $e->getMessage();
        }
    }
    redirect('/admin_companies.php');
}

// Fetch all companies with job count
$stmt = $pdo->query("SELECT cp.*, u.email, 
                    (SELECT COUNT(*) FROM jobs WHERE company_id = cp.id) as job_count 
                    FROM company_profiles cp 
                    JOIN users u ON cp.user_id = u.id 
                    ORDER BY cp.name ASC");
$companies = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa vystavovatelů</h2>
    <a href="/admin_company_add.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-circle me-2"></i>Přidat novou firmu
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Název firmy</th>
                    <th>Kontakt</th>
                    <th>Typ</th>
                    <th>Pairing Code</th>
                    <th class="text-center">Pozice</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($companies)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Zatím nejsou registrováni žádní vystavovatelé.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($companies as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= e($c['name']) ?></div>
                                <div class="small text-muted"><?= e($c['email']) ?></div>
                            </td>
                            <td><?= e($c['contact_person']) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $c['type'] === 'physical' ? 'bg-info text-dark' : 'bg-secondary' ?>">
                                    <?= $c['type'] === 'physical' ? 'Fyzicky' : 'Virtuálně' ?>
                                </span>
                            </td>
                            <td><code class="fw-bold text-primary"><?= e($c['pairing_code']) ?></code></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?= $c['job_count'] ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/admin_company_edit.php?id=<?= $c['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary border-0">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/admin_companies.php?delete=<?= $c['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('Opravdu chcete smazat firmu <?= e($c['name']) ?> a její uživatelský účet?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 text-center text-muted small">
    <p>Smazáním firmy dojde k trvalému odstranění všech jejích pozic, schůzek a uživatelského přístupu.</p>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
