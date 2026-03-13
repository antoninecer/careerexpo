Základní struktura kódu pro vaše požadavky je následující:

```php
<?php
// 1. Require candidate role
session_start();
if (!isset($_SESSION['candidate_id'])) {
    header('Location: login.php');
    exit();
}

// 2. Načíst ID z GET
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

// 3. Připojení k databázi
try {
    $db = new PDO('mysql:host=localhost;dbname=CareerExpo;charset=utf8', 'username', 'password');
} catch (PDOException $e) {
    die("Chyba: " . $e->getMessage());
}

$stmt = $db->prepare("SELECT j.*, cp.* 
                      FROM jobs j 
                      JOIN company_profiles cp ON j.company_id = cp.company_id
                      WHERE j.id = :id");
$stmt->execute(['id' => $_GET['id']]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header('Location: index.php');
    exit();
}

// 4. Zobrazit detaily 
$title = $job['title'];
$description = $job['description'];
$location = $job['location'];
$company = $job['company_name'];
$salary = $job['salary'];

// 5. Použít templates/header.php a footer.php
require_once('templates/header.php');

// 6. Zobrazit detaily
echo "<h1>$title</h1>";
echo "<p>$description</p>";
echo "<p>Lokalita: $location</p>";
echo "<p>Firma: $company</p>";
echo "<p>Platové rozpětí: $salary</p>";

require_once('templates/footer.php');
```

Nezapomeňte si změnit 'username' a 'password' v PDO na skutečné přihlašovací údaje k vaší databázi. Také může být nutné upravit cestu k header.php a footer.php na správnou relativní cestu v závislosti na vašem projektu.


