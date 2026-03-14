Zde je čistý PHP kód, který vytvoří soubor public/admin_companies.php pro projekt CareerExpo:

```php
<?php
require_once 'bootstrap.php';

// Ensure the user is an admin
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$users = new Users();
$companies = new Companies();

if (isset($_GET['delete'])) {
    $companies->deleteCompany($_GET['delete']);
    $users->deleteUser($_GET['delete']);
    header('Location: admin_companies.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Companies</title>
</head>
<body>
    <h1>Admin Companies</h1>
    <a href="admin_add_company.php" class="btn btn-primary">Přidat novou firmu</a>
    <table class="table">
        <thead>
            <tr>
                <th>Název</th>
                <th>Kontakt</th>
                <th>Typ</th>
                <th>Pairing Code</th>
                <th>Počet pozic</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies->getAllCompanies() as $company): ?>
            <tr>
                <td><?= $company['name'] ?></td>
                <td><?= $company['contact'] ?></td>
                <td><?= $company['type'] ?></td>
                <td><?= $company['pairing_code'] ?></td>
                <td><?= $companies->countJobs($company['id']) ?></td>
                <td>
                    <a href="admin_companies.php?delete=<?= $company['id'] ?>" class="btn btn-danger">Smazat</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha1/js/bootstrap.min.js"></script>
</body>
</html>
```

Pozor, tento kód pouze vytvoří stránku pro správu firmám. Můžeš ho upravit na základě potřeb tvého projektu.

Další požadavky, jako je vytvoření nové firmy nebo její úprava, musí být implementovány jinak.

Pokud používáš třídu Users a Companies, je třeba, aby existovaly metody deleteUser a deleteCompany.

Na poslední, pokud chceš, můžeš také přidat funkci pro úpravu firmám.


