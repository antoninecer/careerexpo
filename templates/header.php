<?php
// templates/header.php
?>
<!DOCTYPE html>
<html lang='cs'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?= e(APP_NAME) ?></title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-green { background-color: #198754; color: white; }
        .badge-orange { background-color: #fd7e14; color: white; }
        .badge-red { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-lg navbar-dark bg-dark mb-4'>
        <div class='container'>
            <a class='navbar-brand' href='/index.php'><?= e(APP_NAME) ?></a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-content='navbarNav'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarNav'>
                <ul class='navbar-nav ms-auto small'>
                    <?php if (isLoggedIn()): ?>
                        <?php if (getCurrentEventId()): 
                            \$stmt = \$pdo->prepare(\"SELECT name FROM events WHERE id = ?\");
                            \$stmt->execute([getCurrentEventId()]);
                            \$eventName = \$stmt->fetchColumn();
                        ?>
                            <li class='nav-item'><span class='nav-link text-warning fw-bold'><i class='bi bi-calendar-event'></i> <?= e(\$eventName) ?></span></li>
                            <li class='nav-item'><a class='nav-link' href='/events.php'><i class='bi bi-shuffle'></i> Přepnout akci</a></li>
                            <li class='nav-item border-start ms-2 ps-2'><a class='nav-link' href='/dashboard.php'>Dashboard</a></li>
                            <?php if (hasRole('candidate')): ?>
                                <li class='nav-item'><a class='nav-link' href='/stands.php'>Mapa stánků</a></li>
                                <li class='nav-item'><a class='nav-link' href='/lectures.php'>Přednášky</a></li>
                                <li class='nav-item'><a class='nav-link' href='/meetings.php'>Moje schůzky</a></li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class='nav-item'><a class='nav-link' href='/events.php'>Vybrat akci</a></li>
                        <?php endif; ?>
                        <li class='nav-item'><a class='nav-link text-danger' href='/logout.php'>Odhlásit</a></li>
                    <?php else: ?>
                        <li class='nav-item'><a class='nav-link' href='/login.php'>Přihlášení</a></li>
                        <li class='nav-item'><a class='nav-link' href='/register.php'>Registrace</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class='container pb-5'>
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($_SESSION['flash_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= e($_SESSION['flash_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
