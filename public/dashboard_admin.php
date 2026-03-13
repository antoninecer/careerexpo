<?php
// Included in public/dashboard.php
// role: admin

require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('admin');

// Global metrics
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'candidate'");
$countCandidates = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'company'");
$countCompanies = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM jobs");
$countJobs = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM candidate_files");
$countCVs = $stmt->fetchColumn();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2 class="text-primary fw-bold">Administrátorský Dashboard</h2>
    </div>

    <!-- Metrics -->
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100 shadow-sm border-0">
            <h2 class="text-primary fw-bold"><?= (int)$countCandidates ?></h2>
            <p class="mb-0 text-muted small">Uchazeči</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100 shadow-sm border-0">
            <h2 class="text-success fw-bold"><?= (int)$countCompanies ?></h2>
            <p class="mb-0 text-muted small">Firmy</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100 shadow-sm border-0">
            <h2 class="text-info fw-bold"><?= (int)$countJobs ?></h2>
            <p class="mb-0 text-muted small">Pozice</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100 shadow-sm border-0">
            <h2 class="text-warning fw-bold"><?= (int)$countCVs ?></h2>
            <p class="mb-0 text-muted small">Nahraná CV</p>
        </div>
    </div>

    <!-- Success Metrics -->
    <div class="col-md-12 mb-4">
        <div class="card p-4 shadow-sm border-0 bg-light">
            <h4 class="fw-bold text-dark mb-4">Dopad veletrhu (Real-time Success)</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM meetings WHERE outcome = 'hired'");
                    $countHired = $stmt->fetchColumn();
                    ?>
                    <div class="p-3 bg-white rounded shadow-sm text-center border-start border-5 border-success">
                        <h2 class="text-success fw-bold mb-0"><?= (int)$countHired ?></h2>
                        <p class="mb-0 text-muted fw-bold small">PLÁCLI JSME SI! 🤝</p>
                        <small class="text-muted">Uzavřené kontrakty</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM meetings WHERE outcome = 'offer_made'");
                    $countOffers = $stmt->fetchColumn();
                    ?>
                    <div class="p-3 bg-white rounded shadow-sm text-center border-start border-5 border-primary">
                        <h2 class="text-primary fw-bold mb-0"><?= (int)$countOffers ?></h2>
                        <p class="mb-0 text-muted fw-bold small">NABÍDKA PODÁNA</p>
                        <small class="text-muted">Kandidáti v procesu</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM meetings WHERE status = 'confirmed'");
                    $countConfirmed = $stmt->fetchColumn();
                    ?>
                    <div class="p-3 bg-white rounded shadow-sm text-center border-start border-5 border-info">
                        <h2 class="text-info fw-bold mb-0"><?= (int)$countConfirmed ?></h2>
                        <p class="mb-0 text-muted fw-bold small">SCHŮZKY PROBĚHLY</p>
                        <small class="text-muted">Celkem realizovaných setkání</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card p-4 shadow-sm border-0 h-100">
            <h4 class="fw-bold mb-3">Správa Vystavovatelů</h4>
            <div class="d-grid gap-2">
                <a href="/admin_companies.php" class="btn btn-outline-primary rounded-pill">Seznam firem</a>
                <a href="/admin_company_add.php" class="btn btn-primary rounded-pill">Přidat novou firmu</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card p-4 shadow-sm border-0 h-100">
            <h4 class="fw-bold mb-3">Program a Stánky</h4>
            <div class="d-grid gap-2">
                <a href="/admin_lectures.php" class="btn btn-outline-info rounded-pill">Spravovat přednášky</a>
                <a href="/admin_stands.php" class="btn btn-outline-secondary rounded-pill">Spravovat stánky</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
