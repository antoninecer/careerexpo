<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();
requireEvent();

$eventId = getCurrentEventId();

// Handle Deletion
if (isset($_GET['delete'])) {
    $lectureId = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM lectures WHERE id = ? AND event_id = ?");
        $stmt->execute([$lectureId, $eventId]);
        $_SESSION['flash_success'] = 'Přednáška byla smazána.';
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při mazání: ' . $e->getMessage();
    }
    redirect('/admin_lectures.php');
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lecture'])) {
    validateCsrf();
    $title = $_POST['title'];
    $speaker = $_POST['speaker'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $starts_at = $_POST['starts_at'];
    $capacity = (int)$_POST['capacity'];
    $is_virtual = isset($_POST['is_virtual']) ? 1 : 0;
    $stream_url = $_POST['stream_url'];

    try {
        $stmt = $pdo->prepare("INSERT INTO lectures (event_id, title, speaker, description, location, starts_at, capacity, is_virtual, stream_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$eventId, $title, $speaker, $description, $location, $starts_at, $capacity, $is_virtual, $stream_url]);
        $_SESSION['flash_success'] = "Přednáška '$title' byla vytvořena.";
        redirect('/admin_lectures.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při vytváření: ' . $e->getMessage();
    }
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_lecture'])) {
    validateCsrf();
    $id = (int)$_POST['lecture_id'];
    $title = $_POST['title'];
    $speaker = $_POST['speaker'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $starts_at = $_POST['starts_at'];
    $capacity = (int)$_POST['capacity'];
    $is_virtual = isset($_POST['is_virtual']) ? 1 : 0;
    $stream_url = $_POST['stream_url'];

    try {
        $stmt = $pdo->prepare("UPDATE lectures SET title = ?, speaker = ?, description = ?, location = ?, starts_at = ?, capacity = ?, is_virtual = ?, stream_url = ? WHERE id = ? AND event_id = ?");
        $stmt->execute([$title, $speaker, $description, $location, $starts_at, $capacity, $is_virtual, $stream_url, $id, $eventId]);
        $_SESSION['flash_success'] = "Přednáška '$title' byla upravena.";
        redirect('/admin_lectures.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Chyba při úpravě: ' . $e->getMessage();
    }
}

// Fetch all lectures for current event
$stmt = $pdo->prepare("SELECT l.*, 
                            (SELECT COUNT(*) FROM lecture_reservations WHERE lecture_id = l.id) as reservations_count
                      FROM lectures l 
                      WHERE l.event_id = ? 
                      ORDER BY l.starts_at ASC");
$stmt->execute([$eventId]);
$lectures = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">Správa přednášek a workshopů</h2>
    <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addLectureModal">
        <i class="bi bi-plus-circle me-2"></i>Nová přednáška
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Čas</th>
                    <th>Název a Přednášející</th>
                    <th>Místo</th>
                    <th>Obsazenost</th>
                    <th>Typ</th>
                    <th class="text-end">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lectures)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Pro tuto akci zatím nejsou vytvořeny žádné přednášky.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lectures as $l): ?>
                        <tr>
                            <td><?= date('H:i', strtotime($l['starts_at'])) ?></td>
                            <td>
                                <div class="fw-bold"><?= e($l['title']) ?></div>
                                <div class="small text-muted"><?= e($l['speaker']) ?></div>
                            </td>
                            <td><?= e($l['location']) ?></td>
                            <td>
                                <div class="small mb-1"><?= $l['reservations_count'] ?> / <?= $l['capacity'] ?></div>
                                <div class="progress" style="height: 4px; width: 100px;">
                                    <div class="progress-bar bg-primary" style="width: <?= ($l['capacity'] > 0 ? ($l['reservations_count']/$l['capacity'])*100 : 0) ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($l['is_virtual']): ?>
                                    <span class="badge bg-primary rounded-pill">Online</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Prezenční</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info border-0" title="Upravit přednášku" 
                                            onclick='editLecture(<?= json_encode($l) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="/admin_lectures.php?delete=<?= $l['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('Opravdu chcete smazat tuto přednášku?');">
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

<!-- Add Lecture Modal -->
<div class="modal fade" id="addLectureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Přidat novou přednášku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="add_lecture" value="1">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label small fw-bold">Název přednášky</label>
                            <input type="text" name="title" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Přednášející</label>
                            <input type="text" name="speaker" class="form-control rounded-pill" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Popis</label>
                        <textarea name="description" class="form-control rounded-4" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Místo / Sál</label>
                            <input type="text" name="location" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Čas zahájení</label>
                            <input type="datetime-local" name="starts_at" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Kapacita</label>
                            <input type="number" name="capacity" class="form-control rounded-pill" required value="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_virtual" id="is_virtual">
                            <label class="form-check-label small fw-bold" for="is_virtual">Přednáška probíhá i online (Stream)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">URL Streamu (volitelné)</label>
                        <input type="url" name="stream_url" class="form-control rounded-pill" placeholder="https://youtube.com/...">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Uložit přednášku</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lecture Modal -->
<div class="modal fade" id="editLectureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Upravit přednášku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="edit_lecture" value="1">
                    <input type="hidden" name="lecture_id" id="edit_lecture_id">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label small fw-bold">Název přednášky</label>
                            <input type="text" name="title" id="edit_lecture_title" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Přednášející</label>
                            <input type="text" name="speaker" id="edit_lecture_speaker" class="form-control rounded-pill" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Popis</label>
                        <textarea name="description" id="edit_lecture_description" class="form-control rounded-4" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Místo / Sál</label>
                            <input type="text" name="location" id="edit_lecture_location" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Čas zahájení</label>
                            <input type="datetime-local" name="starts_at" id="edit_lecture_starts_at" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Kapacita</label>
                            <input type="number" name="capacity" id="edit_lecture_capacity" class="form-control rounded-pill" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_virtual" id="edit_is_virtual">
                            <label class="form-check-label small fw-bold" for="edit_is_virtual">Přednáška probíhá i online (Stream)</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">URL Streamu (volitelné)</label>
                        <input type="url" name="stream_url" id="edit_stream_url" class="form-control rounded-pill" placeholder="https://youtube.com/...">
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
function editLecture(lecture) {
    document.getElementById('edit_lecture_id').value = lecture.id;
    document.getElementById('edit_lecture_title').value = lecture.title;
    document.getElementById('edit_lecture_speaker').value = lecture.speaker;
    document.getElementById('edit_lecture_description').value = lecture.description;
    document.getElementById('edit_lecture_location').value = lecture.location;
    
    if (lecture.starts_at) {
        const date = new Date(lecture.starts_at);
        const tzoffset = (new Date()).getTimezoneOffset() * 60000;
        const localISOTime = (new Date(date - tzoffset)).toISOString().slice(0, 16);
        document.getElementById('edit_lecture_starts_at').value = localISOTime;
    }
    
    document.getElementById('edit_lecture_capacity').value = lecture.capacity;
    document.getElementById('edit_is_virtual').checked = lecture.is_virtual == 1;
    document.getElementById('edit_stream_url').value = lecture.stream_url || '';
    
    new bootstrap.Modal(document.getElementById('editLectureModal')).show();
}
</script>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
