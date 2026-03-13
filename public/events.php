Základním přístupem je, že budeme potřebovat načíst všechny akce z databáze, zkontrolovat, zda je uživatel přihlášen ke konkrétní akci, a pak jej přesměrovat na správnou stránku. Zde je základní struktura souboru `public/events.php`:

```php
<?php
require_once('config.php');
require_once('User.php');
require_once('Event.php');

$user = new User();
$event = new Event();

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$events = $event->getAllEvents();

foreach ($events as $event) {
    $registered = $user->isRegisteredForEvent($event['id']);
    if ($registered) {
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'.$event['name'].'</h5>';
        echo '<p class="card-text">'.$event['description'].'</p>';
        echo '<a href="dashboard.php" class="btn btn-primary">Vstoupit do akce</a>';
        echo '<a href="unregister.php?event_id='.$event['id'].'" class="btn btn-secondary">Odhlásit se z aktuální akce</a>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'.$event['name'].'</h5>';
        echo '<p class="card-text">'.$event['description'].'</p>';
        echo '<a href="register.php?event_id='.$event['id'].'" class="btn btn-primary">Zaregistrovat se a vstoupit</a>';
        echo '</div>';
        echo '</div>';
    }
}
?>
```

Upozornění:
1. Tento příklad předpokládá, že jste měli váš uživatelský model `User` a vaše třída `Event` rozšířená o metody `isLoggedIn()`, `isRegisteredForEvent()` a `getAllEvents()`.
2. Pro správné fungování také potřebujete připojení k databázi.
3. V tomto příkladu se předpokládá, že jste měli váš šablony `header.php` a `config.php`, které definují nastavení databáze.
4. Obsahuje odkazy `register.php` a `unregister.php`, které by měly být zaměněny za vaše stránky pro registraci a odhlášení.


