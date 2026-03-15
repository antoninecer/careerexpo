<?php
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/src/MatchingService.php';

use App\MatchingService;

$companyCount = max(5, (int)($argv[1] ?? 18));
$candidateCount = max(10, (int)($argv[2] ?? 140));
$jobsPerCompany = max(1, (int)($argv[3] ?? 3));
$resetDemoData = in_array('--reset-demo', $argv, true);

$fakerFirst = ['Jan','Petr','Martin','Tomáš','Lukáš','Marek','Jiří','David','Jakub','Ondřej','Michal','Daniel','Anna','Eva','Tereza','Lucie','Petra','Jana','Klára','Barbora','Veronika','Adéla','Kristýna'];
$fakerLast = ['Novák','Svoboda','Dvořák','Černý','Procházka','Kučera','Veselý','Horák','Němec','Pospíšil','Hájek','Král','Fiala','Sedláček','Beneš','Pokorný','Navrátil','Urban','Kolář','Bartoš'];
$companyPrefixes = ['Blue','Next','Open','Cloud','Quantum','Core','Delta','Bright','Solid','Future','North','Smart','Prime','Apex','Orbit','Nova','Green','Bold','Vision','Data'];
$companySuffixes = ['Systems','Labs','Works','Solutions','Dynamics','Cloud','Logix','Partners','Soft','Networks','Stack','Factory','Hub','Digital','Forge'];
$locations = ['Praha','Brno','Ostrava','Plzeň','Olomouc','Liberec','Pardubice','Hradec Králové','Remote'];
$interestAreas = [
    'Linux infrastruktura a automatizace',
    'Backend vývoj a integrace',
    'Frontend a UX',
    'Data engineering a reporting',
    'DevOps, CI/CD a observabilita',
    'Cloud, virtualizace a bezpečnost',
    'Obchod a zákaznická péče',
    'Produktový management a analýza'
];
$skillPools = [
    ['Linux','Bash','Ansible','Docker','Kubernetes','Terraform','Git'],
    ['PHP','Laravel','MySQL','REST','Git','Redis','NGINX'],
    ['JavaScript','TypeScript','React','Vue','HTML','CSS','REST'],
    ['Python','SQL','Pandas','ETL','Airflow','PostgreSQL','API'],
    ['AWS','Azure','OpenStack','VMware','Prometheus','Grafana','GitLab CI'],
    ['Sales','CRM','Negotiation','Presentation','Communication','Lead generation'],
    ['QA','Playwright','Cypress','Test cases','Automation','API testing'],
];
$jobTitles = [
    'Junior Linux Admin', 'Mid Linux Admin', 'Senior Linux Admin',
    'Backend PHP Developer', 'Fullstack Developer', 'DevOps Engineer',
    'Cloud Engineer', 'Infrastructure Specialist', 'QA Automation Engineer',
    'Data Analyst', 'Technical Support Engineer', 'Solutions Architect',
    'Product Analyst', 'Business Developer'
];
$seniorityValues = ['junior','mid','senior','expert'];
$collabValues = ['remote','hybrid','onsite'];
$statusValues = ['yes','maybe','contact_later','no'];
$outcomeValues = ['pending','no_response','rejected','offer_made','hired'];

function randItem(array $items) {
    return $items[array_rand($items)];
}

function randItems(array $items, int $min = 2, int $max = 5): array {
    $count = random_int($min, min($max, count($items)));
    shuffle($items);
    return array_slice($items, 0, $count);
}

function randomPairingCode(PDO $pdo, string $table): string {
    do {
        $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE pairing_code = ?");
        $stmt->execute([$code]);
    } while ((int)$stmt->fetchColumn() > 0);
    return $code;
}

function pickEventType(int $index): string {
    return $index % 3 === 0 ? 'virtual' : 'physical';
}

function ensureUser(PDO $pdo, string $email, string $role, string $password = 'Test123!'): int {
    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE role = VALUES(role)");
    $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $role]);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return (int)$stmt->fetchColumn();
}

function ensureEventRegistration(PDO $pdo, int $userId, int $eventId, string $role): void {
    $stmt = $pdo->prepare("INSERT INTO event_registrations (user_id, event_id, role)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE role = VALUES(role)");
    $stmt->execute([$userId, $eventId, $role]);
}

function wipeDemoData(PDO $pdo): void {
    $pdo->exec("DELETE lr FROM lecture_reservations lr JOIN candidate_profiles cp ON cp.id = lr.candidate_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE m FROM meetings m JOIN candidate_profiles cp ON cp.id = m.candidate_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE pc FROM profile_connections pc JOIN candidate_profiles cp ON cp.id = pc.candidate_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE mt FROM matches mt JOIN candidate_profiles cp ON cp.id = mt.candidate_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE cf FROM candidate_files cf JOIN candidate_profiles cp ON cp.id = cf.candidate_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE cp FROM candidate_profiles cp JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE er FROM event_registrations er JOIN users u ON u.id = er.user_id WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");
    $pdo->exec("DELETE u FROM users u WHERE u.email LIKE 'demo.candidate.%@careerexpo.local'");

    $pdo->exec("DELETE lr FROM lecture_reservations lr JOIN lectures l ON l.id = lr.lecture_id WHERE l.title LIKE 'Demo:%'");
    $pdo->exec("DELETE l FROM lectures l WHERE l.title LIKE 'Demo:%'");

    $pdo->exec("DELETE m FROM meetings m JOIN company_profiles cp ON cp.id = m.company_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE pc FROM profile_connections pc JOIN company_profiles cp ON cp.id = pc.company_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE mt FROM matches mt JOIN jobs j ON j.id = mt.job_id JOIN company_profiles cp ON cp.id = j.company_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE j FROM jobs j JOIN company_profiles cp ON cp.id = j.company_id JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE cp FROM company_profiles cp JOIN users u ON u.id = cp.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE er FROM event_registrations er JOIN users u ON u.id = er.user_id WHERE u.email LIKE 'demo.company.%@careerexpo.local'");
    $pdo->exec("DELETE u FROM users u WHERE u.email LIKE 'demo.company.%@careerexpo.local'");

    $pdo->exec("DELETE s FROM stands s WHERE s.name LIKE 'Demo %'");
    $pdo->exec("DELETE e FROM events e WHERE e.slug IN ('prague-2026','brno-2026') OR e.slug LIKE 'demo-%'");
}

try {
    $pdo->beginTransaction();

    if ($resetDemoData) {
        wipeDemoData($pdo);
        echo "Old demo data removed.\n";
    }

    // Admin
    $adminId = ensureUser($pdo, 'admin@careerexpo.cz', 'admin', 'Admin123!');

    // Events
    $events = [
        [
            'name' => 'Prague Career Expo 2026',
            'slug' => 'prague-2026',
            'description' => 'Hlavní testovací veletrh práce a spolupráce.',
            'start' => '2026-04-15 09:00:00',
            'end' => '2026-04-15 18:00:00',
            'location' => 'Praha',
            'type' => 'hybrid',
        ],
        [
            'name' => 'Brno Tech Hiring Day 2026',
            'slug' => 'brno-2026',
            'description' => 'Druhá testovací akce pro více eventů.',
            'start' => '2026-05-20 10:00:00',
            'end' => '2026-05-20 17:00:00',
            'location' => 'Brno',
            'type' => 'physical',
        ]
    ];

    $eventIds = [];
    foreach ($events as $event) {
        $stmt = $pdo->prepare("INSERT INTO events (name, slug, description, start_date, end_date, location, type)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name), description = VALUES(description),
                start_date = VALUES(start_date), end_date = VALUES(end_date),
                location = VALUES(location), type = VALUES(type)");
        $stmt->execute([$event['name'], $event['slug'], $event['description'], $event['start'], $event['end'], $event['location'], $event['type']]);
        $stmt = $pdo->prepare("SELECT id FROM events WHERE slug = ?");
        $stmt->execute([$event['slug']]);
        $eventId = (int)$stmt->fetchColumn();
        $eventIds[] = $eventId;
        ensureEventRegistration($pdo, $adminId, $eventId, 'admin');
    }

    [$mainEventId, $secondaryEventId] = $eventIds;

    // Stands
    $standIdsByEvent = [];
    foreach ($eventIds as $idx => $eventId) {
        foreach (range(1, max(12, (int)ceil($companyCount * 0.75))) as $s) {
            $standName = 'Demo Stand ' . ($idx + 1) . '-' . str_pad((string)$s, 2, '0', STR_PAD_LEFT);
            $zone = chr(64 + (($s - 1) % 4 + 1));
            $location = 'Hala ' . (($idx + 1)) . ' / ' . $zone . '-' . $s;
            $stmt = $pdo->prepare("SELECT id FROM stands WHERE name = ? AND (event_id <=> ?)");
            $stmt->execute([$standName, $eventId]);
            $existing = $stmt->fetchColumn();
            if ($existing) {
                $standIdsByEvent[$eventId][] = (int)$existing;
                continue;
            }
            $stmt = $pdo->prepare("INSERT INTO stands (name, zone, location, event_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$standName, $zone, $location, $eventId]);
            $standIdsByEvent[$eventId][] = (int)$pdo->lastInsertId();
        }
    }

    // Companies + jobs
    $companyProfileIds = [];
    $jobIds = [];
    for ($i = 1; $i <= $companyCount; $i++) {
        $companyName = randItem($companyPrefixes) . ' ' . randItem($companySuffixes) . ' s.r.o.';
        $email = sprintf('demo.company.%03d@careerexpo.local', $i);
        $userId = ensureUser($pdo, $email, 'company');
        $eventId = ($i % 5 === 0) ? $secondaryEventId : $mainEventId;
        ensureEventRegistration($pdo, $userId, $eventId, 'company');
        if ($i % 7 === 0) {
            ensureEventRegistration($pdo, $userId, $secondaryEventId, 'company');
        }

        $contact = randItem($fakerFirst) . ' ' . randItem($fakerLast);
        $type = pickEventType($i);
        $standId = ($type === 'physical' || $type === 'hybrid') ? randItem($standIdsByEvent[$eventId]) : null;
        $stmt = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingProfile = $stmt->fetchColumn();

        $brochureUrl = 'https://careerexpo.rightdone.eu/uploads/rightdone.pdf';
        $videoUrl = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $meetingUrl = 'https://meet.jit.si/CareerExpo-Demo-' . $i;
        $description = 'Demo firma pro testování aplikace. Nabízí více pozic, schůzky, stánky a virtuální prezentaci.';
        $pairingCode = randomPairingCode($pdo, 'company_profiles');

        if ($existingProfile) {
            $stmt = $pdo->prepare("UPDATE company_profiles
                SET pairing_code = ?, name = ?, contact_person = ?, email = ?, type = ?, description = ?, stand_id = ?,
                    website = ?, video_url = ?, meeting_url = ?, brochure_url = ?
                WHERE id = ?");
            $stmt->execute([$pairingCode, $companyName, $contact, $email, $type, $description, $standId,
                'https://careerexpo.rightdone.eu', $videoUrl, $meetingUrl, $brochureUrl, $existingProfile]);
            $companyProfileId = (int)$existingProfile;
        } else {
            $stmt = $pdo->prepare("INSERT INTO company_profiles
                (user_id, pairing_code, name, contact_person, email, type, description, stand_id, website, video_url, meeting_url, brochure_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $pairingCode, $companyName, $contact, $email, $type, $description, $standId,
                'https://careerexpo.rightdone.eu', $videoUrl, $meetingUrl, $brochureUrl]);
            $companyProfileId = (int)$pdo->lastInsertId();
        }

        $companyProfileIds[] = ['id' => $companyProfileId, 'event_id' => $eventId, 'user_id' => $userId];

        for ($j = 1; $j <= $jobsPerCompany; $j++) {
            $pool = randItem($skillPools);
            $skills = implode(', ', randItems($pool, 3, min(6, count($pool))));
            $title = randItem($jobTitles);
            $seniority = randItem($seniorityValues);
            $collab = randItem($collabValues);
            $loc = $collab === 'remote' ? 'Remote' : randItem(array_diff($locations, ['Remote']));
            $salary = match ($seniority) {
                'junior' => '45 000 - 65 000 Kč',
                'mid' => '65 000 - 95 000 Kč',
                'senior' => '90 000 - 140 000 Kč',
                default => '120 000 - 180 000 Kč',
            };
            $stmt = $pdo->prepare("INSERT INTO jobs
                (company_id, event_id, title, description, skills, seniority, location, collaboration_type, languages, priority, salary_range)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $companyProfileId,
                $eventId,
                $title,
                'Demo pracovní pozice určená pro testování filtrování, matchingu a schůzek v aplikaci.',
                $skills,
                $seniority,
                $loc,
                $collab,
                ($j % 3 === 0 ? 'Čeština, Angličtina' : 'Čeština'),
                ($j % 4 === 0 ? 'high' : ($j % 2 === 0 ? 'medium' : 'low')),
                $salary
            ]);
            $jobIds[] = (int)$pdo->lastInsertId();
        }
    }

    // Candidates
    $candidateProfileIds = [];
    for ($i = 1; $i <= $candidateCount; $i++) {
        $first = randItem($fakerFirst);
        $last = randItem($fakerLast);
        $email = sprintf('demo.candidate.%03d@careerexpo.local', $i);
        $userId = ensureUser($pdo, $email, 'candidate');
        $eventId = ($i % 6 === 0) ? $secondaryEventId : $mainEventId;
        ensureEventRegistration($pdo, $userId, $eventId, 'candidate');
        if ($i % 10 === 0) {
            ensureEventRegistration($pdo, $userId, $secondaryEventId, 'candidate');
        }

        $pool = randItem($skillPools);
        $skills = implode(', ', randItems($pool, 3, min(6, count($pool))));
        $seniority = randItem($seniorityValues);
        $collab = randItem($collabValues);
        $loc = $collab === 'remote' ? randItem($locations) : randItem(array_diff($locations, ['Remote']));
        $stmt = $pdo->prepare("SELECT id FROM candidate_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingProfile = $stmt->fetchColumn();
        $pairingCode = randomPairingCode($pdo, 'candidate_profiles');
        $bio = 'Demo kandidát pro testování párování, meetingů, lecture rezervací a administračních přehledů.';

        if ($existingProfile) {
            $stmt = $pdo->prepare("UPDATE candidate_profiles
                SET pairing_code = ?, first_name = ?, last_name = ?, phone = ?, location = ?, seniority = ?,
                    preferred_collaboration = ?, interest_area = ?, skills = ?, languages = ?, bio = ?,
                    linkedin_url = ?, github_url = ?, portfolio_url = ?, availability = ?, sharing_consent = 1, virtual_sharing_consent = 1
                WHERE id = ?");
            $stmt->execute([
                $pairingCode, $first, $last, '+420 77' . random_int(1000000, 9999999), $loc, $seniority,
                $collab, randItem($interestAreas), $skills, ($i % 2 === 0 ? 'Čeština, Angličtina' : 'Čeština'),
                $bio, 'https://linkedin.com/in/demo-' . $i,
                'https://github.com/demo-' . $i, 'https://portfolio.example/demo-' . $i,
                ($i % 4 === 0 ? 'ihned' : 'do 1 měsíce'), $existingProfile
            ]);
            $candidateProfileId = (int)$existingProfile;
        } else {
            $stmt = $pdo->prepare("INSERT INTO candidate_profiles
                (user_id, pairing_code, first_name, last_name, phone, location, seniority, preferred_collaboration,
                 interest_area, skills, languages, bio, linkedin_url, github_url, portfolio_url, availability,
                 sharing_consent, virtual_sharing_consent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
            $stmt->execute([
                $userId, $pairingCode, $first, $last, '+420 77' . random_int(1000000, 9999999), $loc, $seniority,
                $collab, randItem($interestAreas), $skills, ($i % 2 === 0 ? 'Čeština, Angličtina' : 'Čeština'),
                $bio, 'https://linkedin.com/in/demo-' . $i,
                'https://github.com/demo-' . $i, 'https://portfolio.example/demo-' . $i,
                ($i % 4 === 0 ? 'ihned' : 'do 1 měsíce')
            ]);
            $candidateProfileId = (int)$pdo->lastInsertId();
        }

        $candidateProfileIds[] = ['id' => $candidateProfileId, 'event_id' => $eventId, 'user_id' => $userId];
    }

    // Demo lectures
    $lectureIds = [];
    foreach ([
        [$mainEventId, 'Demo: Jak uspět na technickém pohovoru', 'Recruit Lead', '2026-04-15 11:00:00', 80, 'Sál A'],
        [$mainEventId, 'Demo: Infrastruktura a automatizace v praxi', 'Senior Admin', '2026-04-15 13:00:00', 50, 'Sál B'],
        [$secondaryEventId, 'Demo: Kariéra v regionálních firmách', 'HR Partner', '2026-05-20 12:00:00', 60, 'Brno Hall'],
    ] as $lectureDef) {
        [$eventId, $title, $speaker, $startsAt, $capacity, $location] = $lectureDef;
        $stmt = $pdo->prepare("SELECT id FROM lectures WHERE title = ? AND event_id = ?");
        $stmt->execute([$title, $eventId]);
        $existing = $stmt->fetchColumn();
        if ($existing) {
            $lectureIds[] = (int)$existing;
            continue;
        }
        $stmt = $pdo->prepare("INSERT INTO lectures (title, description, speaker, starts_at, capacity, location, is_virtual, stream_url, event_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, 'Demo přednáška pro naplnění testovacích dat.', $speaker, $startsAt, $capacity, $location, 0, null, $eventId]);
        $lectureIds[] = (int)$pdo->lastInsertId();
    }

    $pdo->commit();

    // Matches outside transaction for simplicity
    $matchingService = new MatchingService($pdo);
    foreach ($jobIds as $jobId) {
        $matchingService->updateAllMatchesForJob($jobId);
    }

    // Connections + meetings + reservations
    $pdo->beginTransaction();

    foreach ($candidateProfileIds as $index => $candidate) {
        $sameEventCompanies = array_values(array_filter($companyProfileIds, fn($c) => $c['event_id'] === $candidate['event_id']));
        if (!$sameEventCompanies) {
            continue;
        }
        shuffle($sameEventCompanies);
        $slice = array_slice($sameEventCompanies, 0, random_int(2, min(5, count($sameEventCompanies))));

        foreach ($slice as $n => $company) {
            $status = randItem($statusValues);
            $outcome = $status === 'yes' ? randItem(['pending','offer_made','hired']) : randItem($outcomeValues);
            $stmt = $pdo->prepare("INSERT INTO profile_connections (candidate_id, company_id, status, outcome, event_id)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status), outcome = VALUES(outcome), event_id = VALUES(event_id)");
            $stmt->execute([$candidate['id'], $company['id'], $status, $outcome, $candidate['event_id']]);

            if ($n < 2 || random_int(1, 100) <= 35) {
                $stmt = $pdo->prepare("SELECT id FROM jobs WHERE company_id = ? AND event_id = ? ORDER BY RAND() LIMIT 1");
                $stmt->execute([$company['id'], $candidate['event_id']]);
                $jobId = $stmt->fetchColumn() ?: null;
                $meetingStatus = randItem(['pending','confirmed','confirmed','cancelled']);
                $meetingOutcome = $meetingStatus === 'cancelled' ? 'pending' : randItem($outcomeValues);
                $suggestedAt = $candidate['event_id'] === $mainEventId
                    ? '2026-04-' . str_pad((string)random_int(10, 16), 2, '0', STR_PAD_LEFT) . ' ' . str_pad((string)random_int(9, 17), 2, '0', STR_PAD_LEFT) . ':00:00'
                    : '2026-05-' . str_pad((string)random_int(18, 20), 2, '0', STR_PAD_LEFT) . ' ' . str_pad((string)random_int(9, 17), 2, '0', STR_PAD_LEFT) . ':30:00';
                $confirmedAt = $meetingStatus === 'confirmed' ? $suggestedAt : null;
                $stmt = $pdo->prepare("INSERT INTO meetings (candidate_id, company_id, job_id, suggested_at, confirmed_at, status, notes, outcome, event_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $candidate['id'], $company['id'], $jobId, $suggestedAt, $confirmedAt, $meetingStatus,
                    'Demo meeting pro testování dashboardů a admin přehledů.', $meetingOutcome, $candidate['event_id']
                ]);
            }
        }

        if (!empty($lectureIds) && random_int(1,100) <= 60) {
            shuffle($lectureIds);
            foreach (array_slice($lectureIds, 0, random_int(1, 2)) as $lectureId) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO lecture_reservations (candidate_id, lecture_id) VALUES (?, ?)");
                $stmt->execute([$candidate['id'], $lectureId]);
            }
        }
    }

    $pdo->commit();

    echo "\nDemo data ready.\n";
    echo "Admin login: admin@careerexpo.cz / Admin123!\n";
    echo "Created or updated: {$companyCount} companies, {$candidateCount} candidates, " . ($companyCount * $jobsPerCompany) . " jobs.\n";
    echo "Events: Prague Career Expo 2026, Brno Tech Hiring Day 2026.\n";
    echo "Run again with reset: php seed_demo.php 18 140 3 --reset-demo\n";

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Seeding failed: " . $e->getMessage() . "\n");
    exit(1);
}

