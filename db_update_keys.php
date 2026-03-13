<?php
require_once __DIR__ . '/inc/bootstrap.php';
try {
    $pdo->exec("ALTER TABLE matches ADD UNIQUE KEY idx_candidate_job (candidate_id, job_id)");
    echo "Unique key added to matches table.\n";
} catch (Exception $e) {
    echo "Unique key might already exist: " . $e->getMessage() . "\n";
}

