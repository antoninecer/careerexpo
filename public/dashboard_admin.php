<?php
// Included in public/dashboard.php
// role: admin

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
        <h2 class="text-primary">Administrátorský Dashboard</h2>
    </div>

    <!-- Metrics -->
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100">
            <h2 class="text-primary fw-bold"><?= $countCandidates ?></h2>
            <p class="mb-0 text-muted">Uchazeči</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100">
            <h2 class="text-success fw-bold"><?= $countCompanies ?></h2>
            <p class="mb-0 text-muted">Firmy</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100">
            <h2 class="text-info fw-bold"><?= $countJobs ?></h2>
            <p class="mb-0 text-muted">Pozice</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card p-3 text-center h-100">
            <h2 class="text-warning fw-bold"><?= $countCVs ?></h2>
            <p class="mb-0 text-muted">Nahraná CV</p>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card p-4">
            <h4>Správa Vystavovatelů</h4>
            <div class="d-grid gap-2">
                <a href="admin_companies.php" class="btn btn-outline-primary">Seznam firem</a>
                <a href="admin_company_add.php" class="btn btn-primary">Přidat novou firmu</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card p-4">
            <h4>Program a Stánky</h4>
            <div class="d-grid gap-2">
                <a href="admin_lectures.php" class="btn btn-outline-info">Spravovat přednášky</a>
                <a href="admin_stands.php" class="btn btn-outline-secondary">Spravovat stánky</a>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

