<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Prosím vyplňte všechna pole.';
    } else {
        $stmt = $pdo->prepare("SELECT id, email, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Fetch and verify profile existence
            $profileFound = false;
            if ($user['role'] === 'candidate') {
                $stmt = $pdo->prepare("SELECT id FROM candidate_profiles WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $profile = $stmt->fetch();
                if ($profile) {
                    $_SESSION['profile_id'] = $profile['id'];
                    $profileFound = true;
                }
            } elseif ($user['role'] === 'company') {
                $stmt = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $profile = $stmt->fetch();
                if ($profile) {
                    $_SESSION['profile_id'] = $profile['id'];
                    $profileFound = true;
                }
            } elseif ($user['role'] === 'admin') {
                $profileFound = true; // Admins don't have separate profiles in this simple schema
            }

            if ($profileFound) {
                redirect('/dashboard.php');
            } else {
                // If profile missing, logout and error
                $_SESSION = [];
                session_destroy();
                $error = 'Váš uživatelský profil nebyl nalezen. Kontaktujte podporu.';
            }
        } else {
            $error = 'Nesprávné přihlašovací údaje.';
        }
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-4 shadow-sm border-0">
            <h2 class="text-center mb-4 fw-bold">Přihlášení</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
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
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">Přihlásit se</button>
                </div>
            </form>
            <div class="text-center mt-3">
                Nemáte ještě účet? <a href="<?= BASE_URL ?>/register.php">Zaregistrovat se jako uchazeč</a>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>
