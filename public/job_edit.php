Základem pro vytvoření tohoto souboru by mělo být následující. Tento příklad je závislý na několika předpokládaných třídách a funkcích, které nemusí existovat ve vašem projektu.

<?php
require_once 'vendor/autoload.php';
require_once 'models/Job.php';
require_once 'services/MatchingService.php';

session_start();

// Require company role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'company') {
    die('Unauthorized');
}

// Load job id from GET, validate ownership
$job_id = $_GET['job_id'] ?? null;
if (!$job_id || !Job::validateOwnership($job_id, $_SESSION['user']['company_id'])) {
    die('Unauthorized');
}

$job = Job::load($job_id);

// CSRF protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update job
    $job->name = $_POST['name'] ?? $job->name;
    $job->description = $_POST['description'] ?? $job->description;
    $job->skills = $_POST['skills'] ?? $job->skills;
    $job->seniority = $_POST['seniority'] ?? $job->seniority;
    $job->location = $_POST['location'] ?? $job->location;
    $job->collaboration_type = $_POST['collaboration_type'] ?? $job->collaboration_type;
    $job->languages = $_POST['languages'] ?? $job->languages;
    $job->priority = $_POST['priority'] ?? $job->priority;
    $job->salary_range = $_POST['salary_range'] ?? $job->salary_range;

    $job->save();

    // Update matches
    MatchingService::updateAllMatchesForJob($job_id);

    header('Location: job_detail.php?job_id=' . $job_id);
    exit;
}

// Render form
require_once 'templates/job_edit.php';

function validateCsrf() {
    // Generate a new CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}
?>

Potom byste měli mít `templates/job_edit.php` soubor, který by mohl vypadat takto:

<?php include 'templates/header.php'; ?>

<h1>Edit Job</h1>

<form method="post">
    <?= getCsrfInput() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= $job->name ?>">
    </div>
    <!-- Add more form fields as needed -->
    <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php include 'templates/footer.php'; ?>

Tento příklad je závislý na několika předpokládaných třídách a funkcích, které nemusí existovat ve vašem projektu. Například třída `Job`, která by měla mít metody `load`, `validateOwnership` a `save`, a také třída `MatchingService`, která by měla mít metodu `updateAllMatchesForJob`.


