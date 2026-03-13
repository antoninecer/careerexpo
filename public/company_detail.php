Vytvořím nyní soubor `public/company_detail.php` pro projekt CareerExpo. Tento soubor bude obsahovat kód, který projektu bude dodávat požadované funkce.

Pokud nejste již v přístupu, ujistěte se, že máte přístup k souboru `public/company_detail.php`. Pokud ano, můžete nadále vytvářet kód.

```php
<?php
require_once 'templates/header.php';

// Role: candidate
$role = 'candidate';

// Načti ID firmy z GET
$company_id = $_GET['company_id'];

// Zobraz profil firmy 
$company = getCompanyDetails($company_id); // hypotetická funkce na načtení detailů o firmě
?>

<div class="container">
    <h1><?= $company['name'] ?></h1>
    <p><?= $company['description'] ?></p>
    <p>Typ: <?= $company['type'] ?></p>
    <p>Stand info: <?= $company['stand_info'] ?></p>

    <?php if ($company['video_url']) { ?>
        <iframe width="560" height="315" src="<?= $company['video_url'] ?>" frameborder="0" allowfullscreen></iframe>
    <?php } ?>

    <?php if ($company['meeting_url']) { ?>
        <a href="<?= $company['meeting_url'] ?>" class="btn btn-primary btn-lg">Vstoupit do virtuální místnosti</a>
    <?php } ?>

    <?php if ($company['brochure_url']) { ?>
        <a href="<?= $company['brochure_url'] ?>" class="btn btn-secondary">Stáhnout brošůru</a>
    <?php } ?>

    <h2>Otevřené pozice této firmy</h2>
    <ul>
        <?php
        $jobs = getCompanyJobs($company_id); // hypotetická funkce na načtení otevřených pozic
        foreach ($jobs as $job) {
            echo "<li><a href='job_detail.php?job_id={$job['id']}'>{$job['title']}</a></li>";
        }
        ?>
    </ul>
</div>

<?php
require_once 'templates/footer.php';
?>
```

Požadovaný kód by měl fungovat pro vaši aplikaci CareerExpo. Je nutné dodržet, že máte na místě hypotetické funkce `getCompanyDetails()` a `getCompanyJobs()`, které byste měli nahradit skutečnými voláními k databázi.


