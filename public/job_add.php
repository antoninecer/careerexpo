<?php
require_once __DIR__ . '/../inc/bootstrap.php';

requireRole('company');

$companyId = $_SESSION['profile_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $title = $_POST['title'];
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $seniority = $_POST['seniority'];
    $location = $_POST['location'];
    $collaboration_type = $_POST['collaboration_type'];
    $languages = $_POST['languages'];
    $priority = $_POST['priority'];
    $salary_range = $_POST['salary_range'];

    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (company_id, title, description, skills, seniority, location, collaboration_type, languages, priority, salary_range) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $companyId, $title, $description, $skills, $seniority, 
            $location, $collaboration_type, $languages, $priority, $salary_range
        ]);

        $success = 'Pozice byla úspěšně vytvořena.';
    } catch (Exception $e) {
        $error = 'Chyba: ' . $e->getMessage();
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4">
            <h2 class="mb-4">Přidat novou pozici</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            
            <form method="post">
                <?= getCsrfInput() ?>
                <div class="mb-3">
                    <label class="form-label">Název pozice</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Popis pozice</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lokalita</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Seniorita</label>
                        <select name="seniority" class="form-select">
                            <option value="junior">Junior</option>
                            <option value="mid">Mid</option>
                            <option value="senior">Senior</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Typ spolupráce</label>
                        <select name="collaboration_type" class="form-select">
                            <option value="onsite">On-site</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="remote">Remote</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priorita</label>
                        <select name="priority" class="form-select">
                            <option value="low">Nízká</option>
                            <option value="medium" selected>Střední</option>
                            <option value="high">Vysoká</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Požadované dovednosti (oddělené čárkou)</label>
                    <input type="text" name="skills" class="form-control" placeholder="např. PHP, Linux, React">
                </div>

                <div class="mb-3">
                    <label class="form-label">Jazykové požadavky</label>
                    <input type="text" name="languages" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label">Mzdové rozpětí</label>
                    <input type="text" name="salary_range" class="form-control">
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Zpět na dashboard</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Vytvořit pozici</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

