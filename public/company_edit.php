<?php
require_once __DIR__ . '/../inc/bootstrap.php';
requireRole('company');

$companyId = $_SESSION['profile_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $website = $_POST['website'];
    $video_url = $_POST['video_url'];
    $meeting_url = $_POST['meeting_url'];
    $brochure_url = $_POST['brochure_url'];

    try {
        $stmt = $pdo->prepare("UPDATE company_profiles SET 
            name = ?, contact_person = ?, email = ?, description = ?, 
            website = ?, video_url = ?, meeting_url = ?, brochure_url = ? 
            WHERE id = ?");
        $stmt->execute([$name, $contact_person, $email, $description, $website, $video_url, $meeting_url, $brochure_url, $companyId]);
        
        $_SESSION['flash_success'] = 'Firemní profil byl úspěšně aktualizován.';
        redirect('/dashboard.php');
    } catch (Exception $e) {
        $error = 'Chyba: ' . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT * FROM company_profiles WHERE id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0">
            <h2 class="mb-4 fw-bold text-primary">Upravit firemní profil</h2>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            
            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold">Název firmy</label>
                    <input type="text" name="name" class="form-control rounded-pill" value="<?= e($company['name']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kontaktní osoba</label>
                        <input type="text" name="contact_person" class="form-control rounded-pill" value="<?= e($company['contact_person']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Firemní E-mail</label>
                        <input type="email" name="email" class="form-control rounded-pill" value="<?= e($company['email']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Popis firmy</label>
                    <textarea name="description" class="form-control" rows="4" style="border-radius: 15px;"><?= e($company['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Webová stránka</label>
                    <input type="url" name="website" class="form-control rounded-pill" value="<?= e($company['website']) ?>">
                </div>
                
                <hr class="my-4">
                <h5 class="fw-bold text-info mb-3">Virtuální stánek</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">YouTube Video URL (embed)</label>
                    <input type="url" name="video_url" class="form-control rounded-pill" placeholder="https://www.youtube.com/embed/..." value="<?= e($company['video_url']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Virtuální místnost (Zoom/Jitsi)</label>
                    <input type="url" name="meeting_url" class="form-control rounded-pill" placeholder="https://meet.jit.si/..." value="<?= e($company['meeting_url']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Odkaz na brožuru (PDF)</label>
                    <input type="url" name="brochure_url" class="form-control rounded-pill" placeholder="Odkaz na soubor..." value="<?= e($company['brochure_url']) ?>">
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Zrušit</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Uložit profil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
