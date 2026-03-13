<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

switch ($_SESSION['user_role']) {
    case 'admin':
        include_once __DIR__ . '/dashboard_admin.php';
        break;
    case 'company':
        include_once __DIR__ . '/dashboard_company.php';
        break;
    case 'candidate':
        include_once __DIR__ . '/dashboard_candidate.php';
        break;
    default:
        die('Neznámá role.');
}

