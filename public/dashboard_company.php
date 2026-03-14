<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');
requireEvent();

$eventId = getCurrentEventId();
$userId = $_SESSION['user_id'];

// Fetch company profile
$stmt = $pdo->prepare("SELECT * FROM company_profiles WHERE user_id = ?");
$stmt->execute([$userId]);
$company = $stmt->fetch();
$_SESSION['profile_id'] = $company['id'];
$companyId = $company['id'];

// Fetch stats for current event
$stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id = ? AND event_id = ?");
$stmt->execute([$companyId, $eventId]);
$jobCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM profile_connections WHERE company_id = ? AND event_id = ?");
$stmt->execute([$companyId, $eventId]);
$connCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM meetings WHERE company_id = ? AND event_id = ? AND outcome = 'hired'");
$stmt->execute([$companyId, $eventId]);
$hiredCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM meetings WHERE company_id = ? AND event_id = ? AND status = 'pending'");
$stmt->execute([$companyId, $eventId]);
$pendingMeetings = $stmt->fetchColumn();

// Fetch jobs for current event
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? AND event_id = ? ORDER BY created_at DESC");
$stmt->execute([$companyId, $eventId]);
$jobs = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 mb-4 shadow-sm border-0">
            <h5 class="card-title text-primary fw-bold">Firemní profil</h5>
            <hr>
            <p class="mb-1"><strong><?= e($company['name']) ?></strong></p>
            <p class="small text-muted mb-3"><?= e($company['email']) ?></p>
            <p class="mb-1 small"><strong>Kontakt:</strong> <?= e($company['contact_person']) ?></p>
            <p class="mb-1 small"><strong>Typ:</strong> <?= $company['type'] === 'physical' ? 'Fyzicky' : 'Virtuálně' ?></p>

            <div class="mt-4 p-3 bg-light rounded text-center">
                <p class="small mb-2 fw-bold">Párovací kód firmy:</p>
                <h3 class="text-primary fw-bold mb-3"><?= e($company['pairing_code']) ?></h3>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($company['pairing_code']) ?>" alt="QR kód" class="img-fluid mb-2 rounded shadow-sm">
                <p class="small text-muted mb-0">Ukažte tento kód uchazeči.</p>
            </div>

            <a href="/company_edit.php" class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-4">Upravit profil / virtuální stánek</a>
        </div>

        <div class="card p-3 mb-4 shadow-sm border-0 bg-primary text-white">
            <h5 class="card-title fw-bold">Rychlé spojení</h5>
            <p class="small mb-3">Zadejte kód kandidáta, kterého máte před sebou:</p>
            <form action="/pair.php" method="post">
                <?= getCsrfInput() ?>
                <input type="text" name="pairing_code" class="form-control mb-2 rounded-pill text-center fw-bold" placeholder="ABC123" required maxlength="6">
                <button type="submit" class="btn btn-light w-100 rounded-pill fw-bold text-primary">Přidat kandidáta</button>
            </form>
        </div>
    </div>

    <div class="col-md-9">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <a href="#positions-list" class="text-decoration-none">
                    <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                        <h2 class="text-primary fw-bold mb-0"><?= (int)$jobCount ?></h2>
                        <p class="small mb-0 text-muted">Aktivní pozice</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="/company_connections.php" class="text-decoration-none">
                    <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                        <h2 class="text-success fw-bold mb-0"><?= (int)$connCount ?></h2>
                        <p class="small mb-0 text-muted">Spojení na akci</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="/company_meetings.php?status=hired" class="text-decoration-none">
                    <div class="card p-3 text-center bg-white shadow-sm h-100 border-0 border-bottom border-5 border-success">
                        <h2 class="text-success fw-bold mb-0"><?= (int)$hiredCount ?></h2>
                        <p class="small mb-0 text-muted fw-bold">PLÁCLI JSME SI!</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="/company_meetings.php?status=pending" class="text-decoration-none">
                    <div class="card p-3 text-center bg-white shadow-sm h-100 border-0">
                        <h2 class="text-warning fw-bold mb-0"><?= (int)$pendingMeetings ?></h2>
                        <p class="small mb-0 text-muted">Nové žádosti</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="card p-4 shadow-sm border-0 mb-4" id="positions-list">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Naše pozice pro tuto akci</h4>
                <a href="/job_add.php" class="btn btn-primary rounded-pill btn-sm px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>Přidat pozici
                </a>
            </div>

            <?php if (empty($jobs)): ?>
                <div class="alert alert-info border-0 small">Zatím jste pro tuto akci nepřidali žádnou pracovní pozici.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pozice</th>
                                <th>Lokalita</th>
                                <th>Seniorita</th>
                                <th>Matches</th>
                                <th class="text-end">Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= e($job['title']) ?></strong></td>
                                    <td><span class="small"><?= e($job['location']) ?></span></td>
                                    <td><span class="badge bg-light text-dark border"><?= e($job['seniority']) ?></span></td>
                                    <td>
                                        <?php
                                        $s = $pdo->prepare("SELECT COUNT(*) FROM matches WHERE job_id = ? AND score >= 70");
                                        $s->execute([$job['id']]);
                                        $matches = $s->fetchColumn();
                                        ?>
                                        <span class="badge bg-success rounded-pill"><?= (int)$matches ?> top matches</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/job_edit.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill">Upravit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>

