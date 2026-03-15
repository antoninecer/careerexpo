<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();
requireEvent();

$eventId = getCurrentEventId();

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    die("Event not found.");
}

// Handle Unregistering from event
if (isset($_GET['unregister'])) {
    $userId = (int)$_GET['unregister'];
    $stmt = $pdo->prepare("DELETE FROM event_registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$userId, $eventId]);
    $_SESSION['flash_success'] = 'Firma byla odhlášena z této akce.';
    redirect('/admin_event_exhibitors.php');
}

// Handle Adding existing company to event
if (isset($_POST['add_to_event'])) {
    validateCsrf();
    $userId = (int)$_POST['user_id'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO event_registrations (user_id, event_id, role) VALUES (?, ?, 'company')");
    $stmt->execute([$userId, $eventId]);
    $_SESSION['flash_success'] = 'Firma byla úspěšně přidána k této akci.';
    redirect('/admin_event_exhibitors.php');
}

// Fetch all companies registered for this event
$stmt = $pdo->prepare("SELECT cp.*, u.email, u.id as user_id,
                    (SELECT COUNT(*) FROM jobs WHERE company_id = cp.id AND event_id = ?) as job_count,
                    s.name as stand_name
                    FROM company_profiles cp 
                    JOIN users u ON cp.user_id = u.id 
                    JOIN event_registrations er ON u.id = er.user_id 
                    LEFT JOIN stands s ON cp.stand_id = s.id
                    WHERE er.event_id = ?
                    ORDER BY cp.name ASC");
$stmt->execute([$eventId, $eventId]);
$exhibitors = $stmt->fetchAll();

// Fetch companies NOT in this event for the dropdown
$stmt = $pdo->prepare("SELECT cp.*, u.id as user_id FROM company_profiles cp 
                      JOIN users u ON cp.user_id = u.id 
                      WHERE u.id NOT IN (SELECT user_id FROM event_registrations WHERE event_id = ?)
                      ORDER BY cp.name ASC");
$stmt->execute([$eventId]);
$availableCompanies = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin_events.php">Všechny akce</a></li>
            <li class="breadcrumb-item active"><?= e($event['name']) ?></li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-primary mb-0">Vystavovatelé na akci: <?= e($event['name']) ?></h2>
        <div class="btn-group">
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addExhibitorModal">
                <i class="bi bi-person-plus me-2"></i>Přiřadit stávající firmu
            </button>
            <a href="/admin_company_add.php" class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Vytvořit novou firmu
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Firma</th>
                    <th>Typ</th>
                    <th>Stánek</th>
                    <th class="text-center">Pozice</th>
                    <th>Detaily</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($exhibitors)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-building-exclamation display-4 mb-3 d-block"></i>
                            K této akci zatím nejsou přiřazeni žádní vystavovatelé.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($exhibitors as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= e($c['name']) ?></div>
                                <div class="small text-muted"><?= e($c['email']) ?></div>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?= $c['type'] === 'physical' ? 'bg-info text-dark' : 'bg-primary' ?>">
                                    <?= $c['type'] === 'physical' ? 'Fyzicky' : 'Virtuálně' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($c['stand_name']): ?>
                                    <span class="badge bg-light text-dark border"><?= e($c['stand_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">Nepřiřazen</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-white text-primary border border-primary"><?= $c['job_count'] ?></span>
                            </td>
                            <td>
                                <div class="small">
                                    <?php if ($c['description']): ?><i class="bi bi-check-circle-fill text-success" title="Popis vyplněn"></i><?php else: ?><i class="bi bi-dash-circle text-muted" title="Chybí popis"></i><?php endif; ?> Popis |
                                    <?php if ($c['video_url']): ?><i class="bi bi-check-circle-fill text-success" title="Video vloženo"></i><?php else: ?><i class="bi bi-dash-circle text-muted" title="Chybí video"></i><?php endif; ?> Video |
                                    <?php if ($c['brochure_url']): ?><i class="bi bi-check-circle-fill text-success" title="PDF nahráno"></i><?php else: ?><i class="bi bi-dash-circle text-muted" title="Chybí PDF"></i><?php endif; ?> PDF
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/admin_company_edit.php?id=<?= $c['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary border-0" title="Upravit profil">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="/admin_event_exhibitors.php?unregister=<?= $c['user_id'] ?>" 
                                       class="btn btn-sm btn-outline-warning border-0" 
                                       onclick="return confirm('Odhlásit firmu <?= e($c['name']) ?> z této akce? Firma samotná nebude smazána.');" title="Odhlásit z akce">
                                        <i class="bi bi-x-circle"></i>
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

<!-- Modal pro přiřazení stávající firmy -->
<div class="modal fade" id="addExhibitorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Přiřadit firmu k akci</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="add_to_event" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Vyberte firmu ze systému</label>
                        <select name="user_id" class="form-select rounded-pill" required>
                            <option value="">-- Vyberte firmu --</option>
                            <?php foreach ($availableCompanies as $ac): ?>
                                <option value="<?= $ac['user_id'] ?>"><?= e($ac['name']) ?> (<?= e($ac['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="alert alert-info border-0 small mb-0">
                        <i class="bi bi-info-circle me-2"></i>Zde se zobrazují pouze firmy, které ještě nejsou k této akci přihlášeny.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Přidat k akci</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
