<?php
require_once __DIR__ . '/inc/bootstrap.php';

echo "Running automated tests...\n";

function assertTest($condition, $message) {
    if ($condition) {
        echo "[PASS] $message\n";
    } else {
        echo "[FAIL] $message\n";
        exit(1);
    }
}

// 1. Test User Registration (Candidate)
$testEmail = 'test_candidate_' . time() . '@example.com';
$testPass = password_hash('TestPass123!', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'candidate')");
$stmt->execute([$testEmail, $testPass]);
$userId = $pdo->lastInsertId();
assertTest($userId > 0, "Candidate registration successful.");

// 2. Test Profile Creation
$stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, first_name, last_name) VALUES (?, 'Test', 'User')");
$stmt->execute([$userId]);
$profileId = $pdo->lastInsertId();
assertTest($profileId > 0, "Candidate profile creation successful.");

// 3. Test Profile Update
$stmt = $pdo->prepare("UPDATE candidate_profiles SET seniority = 'senior', skills = 'PHP, MySQL' WHERE id = ?");
$stmt->execute([$profileId]);
$stmt = $pdo->prepare("SELECT seniority FROM candidate_profiles WHERE id = ?");
$stmt->execute([$profileId]);
$seniority = $stmt->fetchColumn();
assertTest($seniority === 'senior', "Profile update successful.");

// 4. Test Job Creation (Company)
$companyUserEmail = 'test_company_' . time() . '@example.com';
$stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, 'dummy', 'company')");
$stmt->execute([$companyUserEmail]);
$companyUserId = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO company_profiles (user_id, name) VALUES (?, 'Test Company')");
$stmt->execute([$companyUserId]);
$companyId = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO jobs (company_id, title, seniority, skills) VALUES (?, 'Senior PHP Developer', 'senior', 'PHP, MySQL')");
$stmt->execute([$companyId]);
$jobId = $pdo->lastInsertId();
assertTest($jobId > 0, "Job creation successful.");

// 5. Test Matching Service
require_once __DIR__ . '/src/MatchingService.php';
use App\MatchingService;
$matcher = new MatchingService($pdo);
$match = $matcher->calculateMatch($profileId, $jobId);

assertTest($match['score'] >= 80, "Matching logic working correctly (Score: " . $match['score'] . ").");
assertTest($match['color'] === 'green', "Matching color working correctly (Color: " . $match['color'] . ").");

echo "\nAll tests passed successfully!\n";

