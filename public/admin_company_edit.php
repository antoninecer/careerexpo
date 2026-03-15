<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireAdmin();

$companyId = (int)($_GET['id'] ?? 0);
if (!$companyId) {
    redirect('/admin_companies.php');
}

// Načtení profilu firmy a uživatelského účtu
$stmt = $pdo->prepare("SELECT cp.*, u.email, u.id as user_id FROM company_profiles cp JOIN users u ON cp.user_id = u.id WHERE cp.id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

if (!$company) {
    $_SESSION['flash_error'] = 'Firma nebyla nalezena.';
    redirect('/admin_companies.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $type = $_POST['type'];
    $stand_id = !empty($_POST['stand_id']) ? (int)$_POST['stand_id'] : null;
    $new_password = $_POST['new_password'];

    try {
        $pdo->beginTransaction();

        // 1. Aktualizace profilu firmy
        $stmt = $pdo->prepare("UPDATE company_profiles SET name = ?, contact_person = ?, email = ?, type = ?, stand_id = ? WHERE id = ?");
        $stmt->execute([$name, $contact_person, $email, $type, $stand_id, $companyId]);

        // 2. Aktualizace hesla, pokud bylo zadáno
        if (!empty($new_password)) {
            if (strlen($new_password) < 8) {
                throw new Exception('Heslo musí mít alespoň 8 znaků.');
            }
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $company['user_id']]);
            $passwordChanged = true;
        }

        $pdo->commit();
        $msg = "Profil firmy '{$name}' byl úspěšně upraven.";
        if (isset($passwordChanged)) $msg .= " Heslo bylo změněno.";
        
        $_SESSION['flash_success'] = $msg;
        redirect('/admin_companies.php');
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Admin company edit error: " . $e->getMessage());
        $error = 'Chyba při ukládání: ' . $e->getMessage();
    }
}

// Načtení stánků pro přiřazení (filtrováno podle aktuální akce)
$stmt = $pdo->prepare("SELECT * FROM stands WHERE event_id = ? OR event_id IS NULL ORDER BY zone, name");
$stmt->execute([getCurrentEventId()]);
$stands = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="mb-4 fw-bold text-primary">Upravit vystavovatele (Admin)</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger shadow-sm border-0 small"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?= getCsrfInput() ?>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">Název firmy</label>
                    <input type="text" name="name" class="form-control rounded-pill shadow-sm" value="<?= e($company['name']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Kontaktní osoba</label>
                        <input type="text" name="contact_person" class="form-control rounded-pill shadow-sm" value="<?= e($company['contact_person']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Kontaktní e-mail (veřejný)</label>
                        <input type="email" name="email" class="form-control rounded-pill shadow-sm" value="<?= e($company['email']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Typ účasti</label>
                        <select name="type" class="form-select rounded-pill shadow-sm">
                            <option value="physical" <?= $company['type'] === 'physical' ? 'selected' : '' ?>>Fyzická (Stánek)</option>
                            <option value="virtual" <?= $company['type'] === 'virtual' ? 'selected' : '' ?>>Virtuální (Online)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Přiřadit stánek</label>
                        <select name="stand_id" class="form-select rounded-pill shadow-sm">
                            <option value="">-- Bez stánku --</option>
                            <?php foreach ($stands as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $company['stand_id'] == $s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['name']) ?> (<?= e($s['zone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-bold text-danger mb-3"><i class="bi bi-shield-lock me-2"></i>Změna hesla</h5>
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label class="form-label small text-muted">Nové přihlašovací heslo (ponechte prázdné pro beze změny)</label>
                        <input type="text" name="new_password" id="new_password" class="form-control rounded-pill shadow-sm" placeholder="Min. 8 znaků">
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill shadow-sm" onclick="generatePass()">
                            <i class="bi bi-magic me-2"></i>Generovat
                        </button>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded small text-muted border-start border-3 border-info">
                    <i class="bi bi-info-circle me-2"></i>Přihlašovací e-mail: <strong><?= e($company['email']) ?></strong>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="/admin_companies.php" class="btn btn-outline-secondary rounded-pill px-4">Zrušit</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Uložit vše</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generatePass() {
    const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*";
    let pass = "";
    for (let i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('new_password').value = pass;
}
</script>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
