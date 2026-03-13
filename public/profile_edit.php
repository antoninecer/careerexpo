<?php
require_once __DIR__ . '/../inc/bootstrap.php';
require_once __DIR__ . '/../src/UploadHandler.php';

use App\UploadHandler;

requireRole('candidate');

$profileId = $_SESSION['profile_id'];
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch profile
$stmt = $pdo->prepare("SELECT * FROM candidate_profiles WHERE id = ?");
$stmt->execute([$profileId]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $seniority = $_POST['seniority'];
    $preferred_collaboration = $_POST['preferred_collaboration'];
    $skills = $_POST['skills'];
    $interest_area = $_POST['interest_area'];
    $languages = $_POST['languages'];
    $bio = $_POST['bio'];
    $linkedin_url = $_POST['linkedin_url'];
    $github_url = $_POST['github_url'];
    $portfolio_url = $_POST['portfolio_url'];
    $availability = $_POST['availability'];
    $sharing_consent = isset($_POST['sharing_consent']) ? 1 : 0;
    $virtual_sharing_consent = isset($_POST['virtual_sharing_consent']) ? 1 : 0;

    try {
        $pdo->beginTransaction();
        
        // Update profile
        $stmt = $pdo->prepare("UPDATE candidate_profiles SET 
            first_name = ?, last_name = ?, phone = ?, location = ?, seniority = ?, 
            preferred_collaboration = ?, skills = ?, interest_area = ?, languages = ?, 
            bio = ?, linkedin_url = ?, github_url = ?, portfolio_url = ?, availability = ?, 
            sharing_consent = ?, virtual_sharing_consent = ? WHERE id = ?");
        
        $stmt->execute([
            $first_name, $last_name, $phone, $location, $seniority, 
            $preferred_collaboration, $skills, $interest_area, $languages, 
            $bio, $linkedin_url, $github_url, $portfolio_url, $availability, 
            $sharing_consent, $virtual_sharing_consent, $profileId
        ]);

        // Handle CV upload
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploader = new UploadHandler(__DIR__ . '/../uploads/cv');
            $fileData = $uploader->upload($_FILES['cv'], $profileId);
            
            $stmt = $pdo->prepare("INSERT INTO candidate_files (candidate_id, filename, filepath) VALUES (?, ?, ?)");
            $stmt->execute([$profileId, $fileData['filename'], $fileData['filepath']]);
        }

        $pdo->commit();
        $success = 'Profil byl úspěšně aktualizován.';
        
        // Refresh profile data
        $stmt = $pdo->prepare("SELECT * FROM candidate_profiles WHERE id = ?");
        $stmt->execute([$profileId]);
        $profile = $stmt->fetch();
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Chyba: ' . $e->getMessage();
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card p-4">
            <h2 class="mb-4">Upravit profil uchazeče</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <?= getCsrfInput() ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jméno</label>
                        <input type="text" name="first_name" class="form-control" value="<?= e($profile['first_name']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Příjmení</label>
                        <input type="text" name="last_name" class="form-control" value="<?= e($profile['last_name']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($profile['phone']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lokalita</label>
                        <input type="text" name="location" class="form-control" value="<?= e($profile['location']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Seniorita</label>
                        <select name="seniority" class="form-select">
                            <option value="junior" <?= $profile['seniority'] == 'junior' ? 'selected' : '' ?>>Junior</option>
                            <option value="mid" <?= $profile['seniority'] == 'mid' ? 'selected' : '' ?>>Mid</option>
                            <option value="senior" <?= $profile['seniority'] == 'senior' ? 'selected' : '' ?>>Senior</option>
                            <option value="expert" <?= $profile['seniority'] == 'expert' ? 'selected' : '' ?>>Expert</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preferovaná spolupráce</label>
                        <select name="preferred_collaboration" class="form-select">
                            <option value="onsite" <?= $profile['preferred_collaboration'] == 'full-time' ? 'selected' : '' ?>>On-site</option>
                            <option value="hybrid" <?= $profile['preferred_collaboration'] == 'part-time' ? 'selected' : '' ?>>Hybrid</option>
                            <option value="remote" <?= $profile['preferred_collaboration'] == 'contract' ? 'selected' : '' ?>>Remote</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dovednosti (oddělené čárkou)</label>
                    <input type="text" name="skills" class="form-control" placeholder="např. PHP, Linux, React" value="<?= e($profile['skills']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Oblast zájmu</label>
                    <textarea name="interest_area" class="form-control" rows="2"><?= e($profile['interest_area']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jazyky</label>
                    <input type="text" name="languages" class="form-control" value="<?= e($profile['languages']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">O mně / Bio</label>
                    <textarea name="bio" class="form-control" rows="4"><?= e($profile['bio']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control" value="<?= e($profile['linkedin_url']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">GitHub URL</label>
                        <input type="url" name="github_url" class="form-control" value="<?= e($profile['github_url']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Portfolio URL</label>
                        <input type="url" name="portfolio_url" class="form-control" value="<?= e($profile['portfolio_url']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dostupnost (např. ihned, od srpna)</label>
                    <input type="text" name="availability" class="form-control" value="<?= e($profile['availability']) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label">Nahrát CV (PDF, DOC, DOCX)</label>
                    <input type="file" name="cv" class="form-control">
                    <div class="form-text">Doporučujeme PDF. Maximálně 5 MB.</div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="sharing_consent" class="form-check-input" id="sharing_consent" <?= $profile['sharing_consent'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="sharing_consent">Souhlasím se sdílením profilu s fyzicky přítomnými firmami.</label>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="virtual_sharing_consent" class="form-check-input" id="virtual_sharing_consent" <?= $profile['virtual_sharing_consent'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="virtual_sharing_consent">Souhlasím se sdílením profilu i s virtuálními/nepřítomnými firmami.</label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Zrušit</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Uložit změny</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

