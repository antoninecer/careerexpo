<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();
requireEvent();

$eventId = getCurrentEventId();

// Handle Deletion
if (isset($_GET['delete'])) {
    $standId = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM stands WHERE id = ? AND event_id = ?");
        $stmt->execute([$standId, $eventId]);
        $_SESSION['flash_success'] = 'Stánek byl smazán.';
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při mazání: ' . $e->getMessage();
    }
    redirect('/admin_stands.php');
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stand'])) {
    validateCsrf();
    $name = $_POST['name'];
    $zone = $_POST['zone'];
    $location = $_POST['location'];

    try {
        $stmt = $pdo->prepare("INSERT INTO stands (event_id, name, zone, location) VALUES (?, ?, ?, ?)");
        $stmt->execute([$eventId, $name, $zone, $location]);
        $_SESSION['flash_success'] = "Stánek '$name' byl vytvořen.";
        redirect('/admin_stands.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při vytváření: ' . $e->getMessage();
    }
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_stand'])) {
    validateCsrf();
    $id = (int)$_POST['stand_id'];
    $name = $_POST['name'];
    $zone = $_POST['zone'];
    $location = $_POST['location'];

    try {
        $stmt = $pdo->prepare("UPDATE stands SET name = ?, zone = ?, location = ? WHERE id = ? AND event_id = ?");
        $stmt->execute([$name, $zone, $location, $id, $eventId]);
        $_SESSION['flash_success'] = "Stánek '$name' byl upraven.";
        redirect('/admin_stands.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při úpravě: ' . $e->getMessage();
    }
}

// Fetch all stands for current event
$stmt = $pdo->prepare("SELECT s.*, cp.name as company_name 
                      FROM stands s 
                      LEFT JOIN company_profiles cp ON s.id = cp.stand_id 
                      WHERE s.event_id = ? 
                      ORDER BY s.zone, s.name");
$stmt->execute([$eventId]);
$stands = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa stánků</h2>
    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addStandModal">
        <i class="bi bi-plus-circle me-2"></i>Nový stánek
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Název / Číslo</th>
                    <th>Zóna</th>
                    <th>Umístění</th>
                    <th>Obsazeno firmou</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($stands)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Pro tuto akci zatím nejsou vytvořeny žádné stánky.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stands as $s): ?>
                        <tr>
                            <td class="fw-bold"><?= e($s['name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($s['zone']) ?></span></td>
                            <td><?= e($s['location']) ?></td>
                            <td>
                                <?php if ($s['company_name']): ?>
                                    <span class="text-primary fw-bold"><?= e($s['company_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">Volno</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info border-0" title="Upravit stánek" 
                                            onclick='editStand(<?= json_encode($s) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="/admin_stands.php?delete=<?= $s['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('Opravdu chcete smazat tento stánek?');">
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

<!-- Add Stand Modal -->
<div class="modal fade" id="addStandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Přidat nový stánek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="add_stand" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Název / Označení stánku</label>
                        <input type="text" name="name" class="form-control rounded-pill" required placeholder="Např. A15 nebo Stánek 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Zóna / Hala</label>
                        <input type="text" name="zone" class="form-control rounded-pill" required placeholder="Např. Hala 1 nebo Chill-out zóna">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Přesnější popis umístění</label>
                        <input type="text" name="location" class="form-control rounded-pill" placeholder="Např. u hlavního vchodu">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Vytvořit stánek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Stand Modal -->
<div class="modal fade" id="editStandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Upravit stánek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="edit_stand" value="1">
                    <input type="hidden" name="stand_id" id="edit_stand_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Název / Označení stánku</label>
                        <input type="text" name="name" id="edit_stand_name" class="form-control rounded-pill" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Zóna / Hala</label>
                        <input type="text" name="zone" id="edit_stand_zone" class="form-control rounded-pill" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Přesnější popis umístění</label>
                        <input type="text" name="location" id="edit_stand_location" class="form-control rounded-pill">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Uložit změny</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStand(stand) {
    document.getElementById('edit_stand_id').value = stand.id;
    document.getElementById('edit_stand_name').value = stand.name;
    document.getElementById('edit_stand_zone').value = stand.zone;
    document.getElementById('edit_stand_location').value = stand.location;
    
    new bootstrap.Modal(document.getElementById('editStandModal')).show();
}
</script>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
