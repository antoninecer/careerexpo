<?php
// Included in public/dashboard.php
// role: company

$stmt = $pdo->prepare("SELECT * FROM company_profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$company = $stmt->fetch();

// Fetch jobs
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE company_id = ? ORDER BY created_at DESC");
$stmt->execute([$company['id']]);
$jobs = $stmt->fetchAll();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 mb-4">
            <h5 class="card-title text-primary">Můj Firemní Profil</h5>
            <hr>
            <p><strong>Firma:</strong> <?= e($company['name']) ?></p>
            <p><strong>Kontakt:</strong> <?= e($company['contact_person']) ?></p>
            <p><strong>Typ:</strong> <?= $company['type'] === 'physical' ? 'Fyzicky' : 'Virtuálně' ?></p>
            <a href="company_edit.php" class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-2">Upravit profil</a>
            </div>

            <div class="card p-3 mb-4">
            <h5 class="card-title text-primary">Rychlé spojení</h5>
            <p class="small text-muted">Zadejte kód kandidáta pro okamžité spárování:</p>
            <form action="pair.php" method="post">
                <input type="text" name="pairing_code" class="form-control mb-2" placeholder="ABC123" required maxlength="6">
                <button type="submit" class="btn btn-success w-100 rounded-pill">Přidat kandidáta</button>
            </form>
            </div>
            </div>

            <div class="col-md-9">
            <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card p-3 text-center bg-white h-100">
                    <h2 class="text-primary"><?= count($jobs) ?></h2>
                    <p class="mb-0">Otevřené pozice</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <?php
                // Count connections
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM profile_connections WHERE company_id = ?");
                $stmt->execute([$company['id']]);
                $connCount = $stmt->fetchColumn();
                ?>
                <div class="card p-3 text-center bg-white h-100">
                    <h2 class="text-success"><?= $connCount ?></h2>
                    <p class="mb-0">Spojení na místě</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 text-center bg-white h-100">
                    <h2 class="text-info">0</h2>
                    <p class="mb-0">Plánované schůzky</p>
                </div>
            </div>
            </div>

            <!-- Recent connections -->
            <div class="card p-4 mb-4">
            <h4 class="mb-4">Poslední spojení z veletrhu</h4>
            <?php
            $stmt = $pdo->prepare("SELECT pc.*, cp.first_name, cp.last_name, cp.seniority 
                                  FROM profile_connections pc 
                                  JOIN candidate_profiles cp ON pc.candidate_id = cp.id 
                                  WHERE pc.company_id = ? 
                                  ORDER BY pc.created_at DESC LIMIT 5");
            $stmt->execute([$company['id']]);
            $connections = $stmt->fetchAll();
            ?>
            <?php if (empty($connections)): ?>
                <p class="text-muted mb-0">Zatím žádná spojení.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kandidát</th>
                                <th>Seniorita</th>
                                <th>Stav</th>
                                <th>Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($connections as $conn): ?>
                                <tr>
                                    <td><?= e($conn['first_name'] . ' ' . $conn['last_name']) ?></td>
                                    <td><?= e($conn['seniority']) ?></td>
                                    <td><?= e($conn['status']) ?></td>
                                    <td><a href="candidate_detail.php?id=<?= $conn['candidate_id'] ?>" class="btn btn-sm btn-link">Profil</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>

            <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Naše pozice</h4>
                <a href="job_add.php" class="btn btn-primary rounded-pill btn-sm px-4">Přidat pozici</a>
            </div>

            <?php if (empty($jobs)): ?>
                <div class="alert alert-info">Zatím jste nepřidali žádnou pozici.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pozice</th>
                                <th>Lokalita</th>
                                <th>Seniorita</th>
                                <th>Matches</th>
                                <th>Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?= e($job['title']) ?></td>
                                    <td><?= e($job['location']) ?></td>
                                    <td><span class="badge bg-secondary"><?= e($job['seniority']) ?></span></td>
                                    <td><span class="badge badge-green">0</span></td>
                                    <td>
                                        <a href="job_edit.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-link">Upravit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../templates/footer.php';
?>

