<?php
require_once __DIR__ . '/inc/bootstrap.php';

echo "Seeding database for Multi-event CareerExpo...\n";

try {
    $pdo->beginTransaction();

    // 1. Create Admin
    $adminEmail = 'admin@careerexpo.cz';
    $adminPass = password_hash('Admin123!', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, role) VALUES (?, ?, 'admin')");
    $stmt->execute([$adminEmail, $adminPass]);
    $adminId = $pdo->query("SELECT id FROM users WHERE email = '$adminEmail'")->fetchColumn();
    echo "Admin created: $adminEmail\n";

    // 2. Create Default Event
    $stmt = $pdo->prepare("INSERT IGNORE INTO events (name, slug, description, start_date, end_date, location, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Prague Career Expo 2026', 
        'prague-2026', 
        'Hlavní kariérní událost roku.', 
        '2026-03-13 09:00:00', 
        '2026-03-13 18:00:00', 
        'Kongresové centrum Praha', 
        'hybrid'
    ]);
    $eventId = $pdo->query("SELECT id FROM events WHERE slug = 'prague-2026'")->fetchColumn();
    echo "Default event created: Prague Career Expo 2026\n";

    // 3. Register Admin to Event
    $stmt = $pdo->prepare("INSERT IGNORE INTO event_registrations (user_id, event_id, role) VALUES (?, ?, 'admin')");
    $stmt->execute([$adminId, $eventId]);

    // 4. Create Sample Company
    $companyEmail = 'hr@techgiants.cz';
    $companyPass = password_hash('Company123!', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (email, password_hash, role) VALUES (?, ?, 'company')");
    $stmt->execute([$companyEmail, $companyPass]);
    $companyUserId = $pdo->query("SELECT id FROM users WHERE email = '$companyEmail'")->fetchColumn();

    $pairingCode = 'TG' . rand(1000, 9999);
    $stmt = $pdo->prepare("INSERT IGNORE INTO company_profiles (user_id, name, contact_person, email, type, pairing_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$companyUserId, 'Tech Giants s.r.o.', 'Petr Velký', $companyEmail, 'physical', $pairingCode]);
    $companyProfileId = $pdo->query("SELECT id FROM company_profiles WHERE user_id = $companyUserId")->fetchColumn();
    
    // Register company to event
    $stmt = $pdo->prepare("INSERT IGNORE INTO event_registrations (user_id, event_id, role) VALUES (?, ?, 'company')");
    $stmt->execute([$companyUserId, $eventId]);
    echo "Sample company created: Tech Giants s.r.o.\n";

    // 5. Create Sample Job for the company
    $stmt = $pdo->prepare("INSERT IGNORE INTO jobs (company_id, event_id, title, description, seniority, location, collaboration_type, skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $companyProfileId, 
        $eventId, 
        'Senior Linux Admin', 
        'Hledáme experta na automatizaci a infrastrukturu.', 
        'senior', 
        'Praha', 
        'hybrid', 
        'Linux, Ansible, Docker'
    ]);
    echo "Sample job created.\n";

    $pdo->commit();
    echo "\nSeeding complete. You can now login as admin.\n";

} catch (Exception $e) {
    $pdo->rollBack();
    die("Seeding failed: " . $e->getMessage() . "\n");
}
