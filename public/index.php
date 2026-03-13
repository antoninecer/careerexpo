<?php
require_once __DIR__ . '/../inc/bootstrap.php';
include_once __DIR__ . '/../templates/header.php';
?>

<div class="jumbotron text-center py-5">
    <h1 class="display-4 fw-bold mb-4">Vítejte na <?= e(APP_NAME) ?></h1>
    <p class="lead text-muted mb-5">Místo, kde se setkávají talentovaní uchazeči a vizionářské firmy.</p>
    
    <!-- Veřejné statistiky -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-10">
            <div class="card bg-light border-0 shadow-sm p-4">
                <div class="row text-center">
                    <div class="col-md-2 mb-3 mb-md-0">
                        <?php
                        $c = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'candidate'")->fetchColumn();
                        ?>
                        <h3 class="fw-bold text-primary mb-0"><?= $c ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Uchazečů</small>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0 border-start">
                        <?php
                        $c = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'company'")->fetchColumn();
                        ?>
                        <h3 class="fw-bold text-primary mb-0"><?= $c ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Firem</small>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0 border-start">
                        <?php
                        $c = $pdo->query("SELECT COUNT(*) FROM stands")->fetchColumn();
                        ?>
                        <h3 class="fw-bold text-primary mb-0"><?= $c ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Stánků</small>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0 border-start">
                        <?php
                        $c = $pdo->query("SELECT COUNT(*) FROM lectures")->fetchColumn();
                        $cap = $pdo->query("SELECT SUM(capacity) FROM lectures")->fetchColumn();
                        ?>
                        <h3 class="fw-bold text-primary mb-0"><?= $c ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Přednášek (kap. <?= (int)$cap ?>)</small>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0 border-start">
                        <?php
                        $c = $pdo->query("SELECT COUNT(*) FROM meetings WHERE outcome = 'hired'")->fetchColumn();
                        ?>
                        <h3 class="fw-bold text-success mb-0"><?= $c ?></h3>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">🤝 Plácli si!</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
        <?php if (!isLoggedIn()): ?>
            <a href="/register.php" class="btn btn-primary btn-lg px-4 gap-3 rounded-pill">Zaregistrovat se</a>
            <a href="/login.php" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">Přihlásit se</a>
        <?php else: ?>
            <a href="/dashboard.php" class="btn btn-primary btn-lg px-4 gap-3 rounded-pill">Přejít do dashboardu</a>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-5 text-center">
    <div class="col-md-4 mb-4">
        <div class="card h-100 p-4">
            <h3 class="h4 mb-3">Pro uchazeče</h3>
            <p class="text-muted">Nahrajte své CV a nechte se objevit těmi nejlepšími firmami v oboru.</p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 p-4">
            <h3 class="h4 mb-3">Pro firmy</h3>
            <p class="text-muted">Vystavte své pozice a najděte ideální kandidáty pomocí našeho matchingu.</p>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 p-4">
            <h3 class="h4 mb-3">Matching a schůzky</h3>
            <p class="text-muted">Plánujte setkání a schůzky přímo na místě nebo virtuálně.</p>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

