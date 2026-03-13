<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = 'candidate'; // Default self-registration role

    if (empty($email) || empty($password)) {
        $error = 'Prosím vyplňte všechna pole.';
    } elseif ($password !== $password_confirm) {
        $error = 'Hesla se neshodují.';
    } elseif (strlen($password) < 8) {
        $error = 'Heslo musí mít alespoň 8 znaků.';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Uživatel s tímto e-mailem již existuje.';
        } else {
            // Create user
            \$hash = password_hash(\$password, PASSWORD_DEFAULT);
            \$pdo->beginTransaction();
            try {
                \$stmt = \$pdo->prepare(\"INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)\");
                \$stmt->execute([\$email, \$hash, \$role]);
                \$userId = \$pdo->lastInsertId();

                // Create profile
                if (\$role === 'candidate') {
                    \$pairingCode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                    \$stmt = \$pdo->prepare(\"INSERT INTO candidate_profiles (user_id, pairing_code) VALUES (?, ?)\");
                    \$stmt->execute([\$userId, \$pairingCode]);
                }

                \$pdo->commit();
                $success = 'Registrace proběhla úspěšně. Nyní se můžete přihlásit.';
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Něco se nepovedlo: ' . $e->getMessage();
            }
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">Registrace uchazeče</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Heslo</label>
                    <input type="password" name="password" class="form-control" required>
                    <div class="form-text">Alespoň 8 znaků.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Potvrzení hesla</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill">Zaregistrovat se</button>
                </div>
            </form>
            <div class="text-center mt-3">
                Máte již účet? <a href="<?= BASE_URL ?>/login.php">Přihlásit se</a>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

