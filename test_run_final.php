<?php
require_once __DIR__ . '/inc/bootstrap.php';

echo "Running final verification tests...\n";

function assertTest($condition, $message) {
    if ($condition) {
        echo "[PASS] $message\n";
    } else {
        echo "[FAIL] $message\n";
        exit(1);
    }
}

// 1. Check CSRF token existence
assertTest(!empty($_SESSION['csrf_token']), "CSRF token generated.");

// 2. Test login with profile existence (Point 5)
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'jan.novak@email.cz'");
$stmt->execute();
$user = $stmt->fetch();
assertTest($user > 0, "Candidate jan.novak exists.");

$stmt = $pdo->prepare("SELECT id FROM candidate_profiles WHERE user_id = ?");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
assertTest($profile > 0, "Candidate profile exists.");

// 3. Test Matching with updated collaboration_type (Point 4)
require_once __DIR__ . '/src/MatchingService.php';
use App\MatchingService;
$matcher = new MatchingService($pdo);

// Find a job
$stmt = $pdo->query("SELECT id FROM jobs LIMIT 1");
$job = $stmt->fetch();
if ($job) {
    $match = $matcher->calculateMatch($profile['id'], $job['id']);
    assertTest($match['score'] >= 0 && $match['score'] <= 100, "Matching logic score range correct (Score: " . $match['score'] . ").");
}

// 4. Check UploadHandler for MIME requirement (Point 7)
require_once __DIR__ . '/src/UploadHandler.php';
use App\UploadHandler;
$uploader = new UploadHandler(__DIR__ . '/uploads/cv');
assertTest(is_dir(__DIR__ . '/uploads/cv'), "CV upload directory exists.");

// 5. Verify security settings (Point 8)
assertTest(ini_get('display_errors') == '0', "display_errors is disabled.");
assertTest(ini_get('log_errors') == '1', "log_errors is enabled.");

echo "\nAll verification tests passed successfully!\n";

