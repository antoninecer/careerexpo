<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();

// Handle Deletion
if (isset($_GET['delete'])) {
    $eventId = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        $_SESSION['flash_success'] = 'Akce byla smazána.';
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při mazání: ' . $e->getMessage();
    }
    redirect('/admin_events.php');
}

// Handle Select
if (isset($_GET['select'])) {
    $eventId = (int)$_GET['select'];
    $_SESSION['current_event_id'] = $eventId;
    $_SESSION['flash_success'] = 'Akce byla vybrána jako aktivní.';
    redirect('/dashboard.php');
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    validateCsrf();
    $name = $_POST['name'];
    $type = $_POST['type'];
    $start_date = $_POST['start_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    try {
        $stmt = $pdo->prepare("INSERT INTO events (name, type, start_date, location, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $start_date, $location, $description]);
        $_SESSION['flash_success'] = "Akce '$name' byla vytvořena.";
        redirect('/admin_events.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při vytváření: ' . $e->getMessage();
    }
}

// Fetch all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY start_date DESC");
$events = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa akcí (všechny ročníky)</h2>
    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
        <i class="bi bi-plus-circle me-2"></i>Nová akce
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Název akce</th>
                    <th>Datum</th>
                    <th>Typ</th>
                    <th>Lokalita</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Zatím nejsou vytvořeny žádné akce.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $e): ?>
                        <tr class="<?= (getCurrentEventId() == $e['id']) ? 'table-info' : '' ?>">
                            <td>
                                <div class="fw-bold"><?= e($e['name']) ?></div>
                                <?php if (getCurrentEventId() == $e['id']): ?>
                                    <span class="badge bg-info text-dark small">Aktuálně vybraná</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($e['start_date'])) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $e['type'] === 'physical' ? 'bg-secondary' : ($e['type'] === 'virtual' ? 'bg-primary' : 'bg-info text-dark') ?>">
                                    <?= e($e['type']) ?>
                                </span>
                            </td>
                            <td><?= e($e['location']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="/admin_events.php?select=<?= $e['id'] ?>" 
                                       class="btn btn-sm btn-outline-success border-0" title="Vstoupit / Vybrat">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                    <a href="/admin_events.php?delete=<?= $e['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('Opravdu chcete smazat akci <?= e($e['name']) ?>? Smažete tím i všechny registrace, stánky a přednášky!');">
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

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Vytvořit novou akci</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="add_event" value="1">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Název akce</label>
                        <input type="text" name="name" class="form-control rounded-pill" required placeholder="Např. Career Expo 2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Typ</label>
                        <select name="type" class="form-select rounded-pill">
                            <option value="physical">Fyzická</option>
                            <option value="virtual">Virtuální</option>
                            <option value="hybrid">Hybridní</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Datum a čas zahájení</label>
                        <input type="datetime-local" name="start_date" class="form-control rounded-pill" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lokalita</label>
                        <input type="text" name="location" class="form-control rounded-pill" required placeholder="Např. Praha, Výstaviště">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Popis</label>
                        <textarea name="description" class="form-control rounded-4" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Vytvořit akci</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
