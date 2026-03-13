<?php
require_once __DIR__ . '/inc/bootstrap.php';

echo "Seeding database...\n";

// Create admin
$adminEmail = 'admin@careerexpo.cz';
$adminPass = password_hash('Admin123!', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, role) VALUES (?, ?, 'admin')");
$stmt->execute([$adminEmail, $adminPass]);
echo "Admin created: $adminEmail\n";

// Create sample company
$companyEmail = 'hr@company.cz';
$companyPass = password_hash('Company123!', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, role) VALUES (?, ?, 'company')");
$stmt->execute([$companyEmail, $companyPass]);
$userId = $pdo->lastInsertId();

if ($userId) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_profiles (user_id, name, contact_person, email, type, description) VALUES (?, 'Tech Giants s.r.o.', 'Jan Novák', ?, 'physical', 'Přední technologická firma.')");
    $stmt->execute([$userId, $companyEmail]);
    echo "Company created: $companyEmail\n";
}

// Create sample candidate
$candidateEmail = 'jan.novak@email.cz';
$candidatePass = password_hash('Candidate123!', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, role) VALUES (?, ?, 'candidate')");
$stmt->execute([$candidateEmail, $candidatePass]);
$userId = $pdo->lastInsertId();

if ($userId) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO candidate_profiles (user_id, first_name, last_name, location, seniority, preferred_collaboration) VALUES (?, 'Jan', 'Novák', 'Praha', 'mid', 'remote')");
    $stmt->execute([$userId]);
    echo "Candidate created: $candidateEmail\n";
}

echo "\nSeeding complete.\n";

