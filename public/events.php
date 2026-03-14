<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// Handle selection
if (isset($_GET['select'])) {
    $eventId = (int)$_GET['select'];
    
    // Verify registration
    $stmt = $pdo->prepare("SELECT id FROM event_registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$userId, $eventId]);
    if ($stmt->fetch()) {
        $_SESSION['current_event_id'] = $eventId;
        redirect('/dashboard.php');
    } else {
        $_SESSION['flash_error'] = 'K této akci nejste přihlášeni.';
    }
}

// Handle registration
if (isset($_GET['register'])) {
    $eventId = (int)$_GET['register'];
    
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO event_registrations (user_id, event_id, role) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $eventId, $role]);
        $_SESSION['current_event_id'] = $eventId;
        $_SESSION['flash_success'] = 'Registrace k akci proběhla úspěšně.';
        redirect('/dashboard.php');
    } catch (Exception $e) {
        $_SESSION['flash_error'] = 'Registrace selhala: ' . $e->getMessage();
    }
}

// Handle leave
if (isset($_GET['leave'])) {
    unset($_SESSION['current_event_id']);
    redirect('/events.php');
}

// Fetch events
$stmt = $pdo->prepare("SELECT e.*, 
                        (SELECT id FROM event_registrations WHERE user_id = ? AND event_id = e.id) as registration_id
                      FROM events e 
                      ORDER BY e.start_date DESC");
$stmt->execute([$userId]);
$events = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="text-center mb-5">
    <h2 class="fw-bold text-primary">Výběr akce</h2>
    <p class="text-muted">Vyberte si veletrh, do kterého chcete vstoupit.</p>
</div>

<div class="row justify-content-center">
    <?php if (empty($events)): ?>
        <div class="col-md-6">
            <div class="alert alert-info text-center border-0 shadow-sm">Aktuálně nejsou vypsány žádné akce.</div>
        </div>
    <?php else: ?>
        <?php foreach ($events as $e): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0 <?= (getCurrentEventId() == $e['id']) ? 'border-start border-5 border-success' : '' ?>">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge rounded-pill <?= $e['type'] === 'virtual' ? 'bg-primary' : ($e['type'] === 'hybrid' ? 'bg-info text-dark' : 'bg-secondary') ?> px-3">
                                <?= ucfirst($e['type']) ?>
                            </span>
                        </div>
                        <h4 class="card-title fw-bold"><?= e($e['name']) ?></h4>
                        <p class="card-text text-muted small flex-grow-1"><?= nl2br(e($e['description'])) ?></p>
                        
                        <div class="mt-3 small text-dark mb-4">
                            <div class="mb-1"><i class="bi bi-calendar-check me-2"></i><?= date('d.m.Y H:i', strtotime($e['start_date'])) ?></div>
                            <div><i class="bi bi-geo-alt me-2"></i><?= e($e['location']) ?></div>
                        </div>

                        <?php if ($e['registration_id']): ?>
                            <?php if (getCurrentEventId() == $e['id']): ?>
                                <button class="btn btn-success w-100 rounded-pill disabled fw-bold shadow-sm">Jste uvnitř akce</button>
                            <?php else: ?>
                                <a href="/events.php?select=<?= $e['id'] ?>" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">Vstoupit do akce</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/events.php?register=<?= $e['id'] ?>" class="btn btn-outline-primary w-100 rounded-pill fw-bold">Zaregistrovat se a vstoupit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (getCurrentEventId()): ?>
    <div class="text-center mt-5">
        <a href="/events.php?leave=1" class="btn btn-link text-muted">Opustit aktuální akci a vrátit se později</a>
    </div>
<?php endif; ?>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
