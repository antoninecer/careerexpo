<?php
// templates/header.php
?>
<!DOCTYPE html>
<html lang='cs'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?= e(APP_NAME) ?></title>
    <link rel='icon' type='image/png' href='/assets/img/favicon.png'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-green { background-color: #198754; color: white; }
        .badge-orange { background-color: #fd7e14; color: white; }
        .badge-red { background-color: #dc3545; color: white; }
        .rounded-pill { border-radius: 50rem !important; }
        footer { margin-top: auto; }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-lg navbar-dark bg-dark mb-4'>
        <div class='container'>
            <a class='navbar-brand fw-bold' href='/index.php'>
                <img src='/assets/img/favicon.png' alt='Logo' width='30' height='30' class='d-inline-block align-text-top me-2'>
                <?= e(APP_NAME) ?>
            </a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarNav'>
                <ul class='navbar-nav ms-auto small'>
                    <?php if (isLoggedIn()): ?>
                        <?php if (hasRole('admin')): ?>
                            <li class='nav-item dropdown'>
                                <a class='nav-link dropdown-toggle' href='#' id='adminGlobalDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                    <i class='bi bi-globe'></i> Globální Admin
                                </a>
                                <ul class='dropdown-menu dropdown-menu-dark' aria-labelledby='adminGlobalDropdown'>
                                    <li><a class='dropdown-item' href='/admin_events.php'><i class='bi bi-calendar-event'></i> Správa akcí</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class='dropdown-item' href='/admin_companies.php'><i class='bi bi-building'></i> Všichni vystavovatelé</a></li>
                                    <li><a class='dropdown-item' href='/admin_company_add.php'><i class='bi bi-plus-circle'></i> Přidat firmu</a></li>
                                    <li><a class='dropdown-item' href='/admin_candidates.php'><i class='bi bi-people'></i> Všichni uchazeči</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (getCurrentEventId()): 
                            $stmt = $pdo->prepare("SELECT name FROM events WHERE id = ?");
                            $stmt->execute([getCurrentEventId()]);
                            $eventName = $stmt->fetchColumn();
                        ?>
                            <?php if (hasRole('admin')): ?>
                                <li class='nav-item dropdown'>
                                    <a class='nav-link dropdown-toggle text-info fw-bold' href='#' id='adminEventDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <i class='bi bi-layers-half'></i> Správa: <?= e($eventName) ?>
                                    </a>
                                    <ul class='dropdown-menu dropdown-menu-dark' aria-labelledby='adminEventDropdown'>
                                        <li><a class='dropdown-item' href='/dashboard.php'><i class='bi bi-speedometer2'></i> Dashboard akce</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class='dropdown-item' href='/admin_stands.php'><i class='bi bi-geo-alt'></i> Stánky</a></li>
                                        <li><a class='dropdown-item' href='/admin_lectures.php'><i class='bi bi-mic'></i> Přednášky</a></li>
                                        <li><a class='dropdown-item' href='/admin_jobs.php'><i class='bi bi-briefcase'></i> Pozice</a></li>
                                        <li><a class='dropdown-item' href='/admin_meetings.php'><i class='bi bi-chat-dots'></i> Schůzky</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class='dropdown-item text-warning' href='/events.php'><i class='bi bi-shuffle'></i> Přepnout akci</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class='nav-item'><span class='nav-link text-warning fw-bold'><i class='bi bi-calendar-event'></i> <?= e($eventName) ?></span></li>
                                <li class='nav-item'><a class='nav-link' href='/events.php'><i class='bi bi-shuffle'></i> Přepnout akci</a></li>
                                <li class='nav-item border-start ms-2 ps-2'><a class='nav-link' href='/dashboard.php'>Dashboard</a></li>
                                <?php if (hasRole('candidate')): ?>
                                    <li class='nav-item'><a class='nav-link' href='/stands.php'>Mapa stánků</a></li>
                                    <li class='nav-item'><a class='nav-link' href='/lectures.php'>Přednášky</a></li>
                                    <li class='nav-item'><a class='nav-link' href='/meetings.php'>Moje schůzky</a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class='nav-item'><a class='nav-link' href='/events.php'>Vybrat akci</a></li>
                            <li class='nav-item border-start ms-2 ps-2'><a class='nav-link' href='/dashboard.php'>Dashboard</a></li>
                        <?php endif; ?>
                        <li class='nav-item'><a class='nav-link' href='/help.php'><i class='bi bi-question-circle'></i> Nápověda</a></li>
                        <?php if (isLoggedIn()): ?>
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
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= e($_SESSION['flash_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($_SESSION['flash_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
