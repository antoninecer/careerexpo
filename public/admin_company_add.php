<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('admin');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $type = $_POST['type']; // physical / virtual

    if (empty($email) || empty($password) || empty($name)) {
        $error = 'Email, heslo a název firmy jsou povinné údaje.';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Create User
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'company')");
            $stmt->execute([$email, $hash]);
            $userId = $pdo->lastInsertId();

            // 2. Create Company Profile
            $pairingCode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $stmt = $pdo->prepare("INSERT INTO company_profiles (user_id, name, contact_person, email, type, pairing_code) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $contact_person, $email, $type, $pairingCode]);
            
            // 3. Register to current event (if selected in session) or first available
            $eventId = getCurrentEventId();
            if (!$eventId) {
                $eventId = $pdo->query("SELECT id FROM events LIMIT 1")->fetchColumn();
            }
            
            if ($eventId) {
                $stmt = $pdo->prepare("INSERT INTO event_registrations (user_id, event_id, role) VALUES (?, ?, 'company')");
                $stmt->execute([$userId, $eventId]);
            }

            $pdo->commit();
            $_SESSION['flash_success'] = "Firma '$name' byla úspěšně vytvořena.";
            redirect('/admin_companies.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Chyba při vytváření firmy: ' . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-4">
            <h2 class="mb-4 fw-bold text-primary text-center">Přidat nového vystavovatele</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <?= getCsrfInput() ?>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Název firmy</label>
                    <input type="text" name="name" class="form-control rounded-pill" required placeholder="Např. Tech Solutions s.r.o.">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">E-mail (přihlašovací jméno)</label>
                        <input type="email" name="email" class="form-control rounded-pill" required placeholder="hr@firma.cz">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Heslo</label>
                        <input type="password" name="password" class="form-control rounded-pill" required placeholder="Min. 8 znaků">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Kontaktní osoba</label>
                    <input type="text" name="contact_person" class="form-control rounded-pill" placeholder="Jméno a příjmení">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Typ účasti</label>
                    <select name="type" class="form-select rounded-pill">
                        <option value="physical">Fyzická přítomnost (Stánek)</option>
                        <option value="virtual">Virtuální stánek (Online)</option>
                    </select>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill btn-lg shadow-sm">Vytvořit vystavovatele</button>
                    <a href="/admin_companies.php" class="btn btn-outline-secondary rounded-pill">Zpět na seznam</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
