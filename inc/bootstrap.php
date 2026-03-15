<?php
// Error reporting - logging only for production-like
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config
if (!file_exists(__DIR__ . '/connect.php')) {
    die("Configuration file inc/connect.php missing. Please copy inc/connect.template.php to inc/connect.php and fill in your local details.");
}
require_once __DIR__ . '/connect.php';

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function validateCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed.');
        }
    }
}

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHAR;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}

// Multi-event helper
function getCurrentEventId() {
    return $_SESSION['current_event_id'] ?? null;
}

function hasCurrentEvent() {
    return getCurrentEventId() !== null;
}

function getOptionalCurrentEventId() {
    return getCurrentEventId();
}

function requireEventContext() {
    if (isLoggedIn() && !getCurrentEventId()) {
        // Exclude events selection page itself from redirection
        $currentFile = basename($_SERVER['PHP_SELF']);
        if ($currentFile !== 'events.php' && $currentFile !== 'logout.php' && $currentFile !== 'login.php') {
            header("Location: /events.php");
            exit;
        }
    }
}

function requireEvent() {
    requireEventContext();
}

// Global helper functions
function redirect($path) {
    header("Location: " . $path);
    exit;
}

function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function requireRole($role) {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
    if (!hasRole($role)) {
        die('Access denied.');
    }
}

function requireAdmin() {
    requireRole('admin');
}

/**
 * Převede běžnou YouTube URL na embed verzi pro iframe
 */
function getYouTubeEmbedUrl($url) {
    if (empty($url)) return '';
    
    // Pokud už je to embed link, vrátíme ho
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }
    
    $videoId = '';
    
    // youtube.com/watch?v=XXXX
    if (preg_match('/v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
    // youtu.be/XXXX
    elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if ($videoId) {
        return "https://www.youtube.com/embed/" . $videoId;
    }
    
    return $url;
}
