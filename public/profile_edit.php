<?php
require_once __DIR__ . '/../inc/bootstrap.php';
require_once __DIR__ . '/../src/UploadHandler.php';

use App\UploadHandler;

requireRole('candidate');

$profileId = $_SESSION['profile_id'];
$userId = $_SESSION['user_id'];
$error = '';

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
            
            // Delete old CVs first (physical and DB)
            $stmt = $pdo->prepare("SELECT filepath FROM candidate_files WHERE candidate_id = ?");
            $stmt->execute([$profileId]);
            while ($oldFile = $stmt->fetch()) {
                if (file_exists($oldFile['filepath'])) unlink($oldFile['filepath']);
            }
            $pdo->prepare("DELETE FROM candidate_files WHERE candidate_id = ?")->execute([$profileId]);

            // Insert new CV record
            $stmt = $pdo->prepare("INSERT INTO candidate_files (candidate_id, filename, filepath) VALUES (?, ?, ?)");
            $stmt->execute([$profileId, $fileData['filename'], $fileData['filepath']]);
        }

        $pdo->commit();
        $_SESSION['flash_success'] = 'Profil byl úspěšně aktualizován.';
        redirect('/profile_edit.php');
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Chyba: ' . $e->getMessage();
    }
}

// Fetch current CV info
$stmt = $pdo->prepare("SELECT * FROM candidate_files WHERE candidate_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$profileId]);
$currentCv = $stmt->fetch();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 p-4 mb-4">
            <h2 class="mb-4 fw-bold text-primary">Upravit profil uchazeče</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <?= getCsrfInput() ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Jméno</label>
                        <input type="text" name="first_name" class="form-control rounded-pill" value="<?= e($profile['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Příjmení</label>
                        <input type="text" name="last_name" class="form-control rounded-pill" value="<?= e($profile['last_name']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Telefon</label>
                        <input type="text" name="phone" class="form-control rounded-pill" value="<?= e($profile['phone']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Lokalita</label>
                        <input type="text" name="location" class="form-control rounded-pill" value="<?= e($profile['location']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Seniorita</label>
                        <select name="seniority" class="form-select rounded-pill">
                            <option value="junior" <?= $profile['seniority'] == 'junior' ? 'selected' : '' ?>>Junior</option>
                            <option value="mid" <?= $profile['seniority'] == 'mid' ? 'selected' : '' ?>>Mid</option>
                            <option value="senior" <?= $profile['seniority'] == 'senior' ? 'selected' : '' ?>>Senior</option>
                            <option value="expert" <?= $profile['seniority'] == 'expert' ? 'selected' : '' ?>>Expert</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">Preferovaná spolupráce</label>
                        <select name="preferred_collaboration" class="form-select rounded-pill">
                            <option value="onsite" <?= $profile['preferred_collaboration'] == 'onsite' ? 'selected' : '' ?>>On-site</option>
                            <option value="hybrid" <?= $profile['preferred_collaboration'] == 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            <option value="remote" <?= $profile['preferred_collaboration'] == 'remote' ? 'selected' : '' ?>>Remote</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Dovednosti (oddělené čárkou)</label>
                    <input type="text" name="skills" class="form-control rounded-pill" placeholder="např. PHP, Linux, React" value="<?= e($profile['skills']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Oblast zájmu</label>
                    <textarea name="interest_area" class="form-control" rows="2" style="border-radius: 15px;"><?= e($profile['interest_area']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Jazyky</label>
                    <input type="text" name="languages" class="form-control rounded-pill" value="<?= e($profile['languages']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">O mně / Bio</label>
                    <textarea name="bio" class="form-control" rows="4" style="border-radius: 15px;"><?= e($profile['bio']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control rounded-pill" value="<?= e($profile['linkedin_url']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small">GitHub URL</label>
                        <input type="url" name="github_url" class="form-control rounded-pill" value="<?= e($profile['github_url']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold small">Portfolio URL</label>
                        <input type="url" name="portfolio_url" class="form-control rounded-pill" value="<?= e($profile['portfolio_url']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Dostupnost (např. ihned, od srpna)</label>
                    <input type="text" name="availability" class="form-control rounded-pill" value="<?= e($profile['availability']) ?>">
                </div>

                <div class="mb-4 p-3 bg-light rounded shadow-sm border">
                    <label class="form-label fw-bold">Životopis (CV)</label>
                    <input type="file" name="cv" class="form-control mb-2 rounded-pill">
                    <div class="form-text mb-3 small">Doporučujeme PDF. Maximálně 5 MB. Nahráním nového souboru nahradíte ten stávající.</div>
                    
                    <?php if ($currentCv): ?>
                        <div class="p-3 bg-white border rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0 fw-bold text-success"><i class="bi bi-file-earmark-check-fill"></i> <?= e($currentCv['filename']) ?></p>
                                    <small class="text-muted">Nahráno: <?= date('d.m.Y H:i', strtotime($currentCv['created_at'])) ?></small>
                                </div>
                                <div class="btn-group">
                                    <a href="/cv_view.php?id=<?= $currentCv['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">Zobrazit</a>
                                    <a href="/cv_view.php?id=<?= $currentCv['id'] ?>&download=1" class="btn btn-sm btn-outline-secondary">Stáhnout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0 small border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle"></i> Životopis není nahraný.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="sharing_consent" class="form-check-input" id="sharing_consent" <?= $profile['sharing_consent'] ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="sharing_consent">Souhlasím se sdílením profilu s fyzicky přítomnými firmami.</label>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="virtual_sharing_consent" class="form-check-input" id="virtual_sharing_consent" <?= $profile['virtual_sharing_consent'] ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="virtual_sharing_consent">Souhlasím se sdílením profilu i s virtuálními/nepřítomnými firmami.</label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Zrušit</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Uložit profil</button>
                </div>
            </form>
        </div>

        <?php if ($currentCv): ?>
            <div class="card shadow-sm border-0 p-4 border-start border-5 border-danger mb-5">
                <h5 class="text-danger fw-bold">Smazat životopis</h5>
                <p class="small text-muted mb-3">Tato akce trvale smaže váš nahraný soubor z našeho serveru.</p>
                <form action="/cv_delete.php" method="post" onsubmit="return confirm('Opravdu chcete smazat svůj životopis?');">
                    <?= getCsrfInput() ?>
                    <input type="hidden" name="file_id" value="<?= $currentCv['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4">Smazat CV</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/header.php'; ?>
