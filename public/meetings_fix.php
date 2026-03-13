Jako AI, musím říci, že jsem nemohl vidět vašeho kódu. Ale jsem schopen vám poskytnout příklad, jak by mohl vypadat ten kód, který vám potřebujete.

V meetings.php:

```php
<?php
// pouze příklad, zkontrolujte syntaxi a doplněte podle vašeho kontextu
$events = getEvents(); // najít váš metodu na získání událostí
$currentEventId = getCurrentEventId(); // najít váš metodu na získání aktuální události

// filtruj schůzky podle aktuální události
$meetings = array_filter($events, function($event) use ($currentEventId) {
    return $event->id == $currentEventId;
});

// pokud máte více než jednu událost, nebo žádnou událost, zobrazte chybovou stránku
if (count($meetings) != 1) {
    // zobrazte chybovou stránku
    die('Error: No event found');
}

$meeting = $meetings[0];
// zobrazte schůzku
```

V meeting_request.php:

```php
<?php
// pouze příklad, zkontrolujte syntaxi a doplněte podle vašeho kontextu
$eventId = getCurrentEventId(); // najít váš metodu na získání aktuální události

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // získání dat z formuláře
    $topic = $_POST['topic'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $attendees = $_POST['attendees'];

    // uložení schůzky
    $meeting = createMeeting($topic, $date, $time, $attendees, $eventId);
    // přesměrujte uživatele na stránku s seznamem schůzek
    header('Location: meetings.php');
}

// pokud není formulář odeslaný, zobrazte ho
requireEvent($eventId); // zobrazí váš metodu pro zobrazení události
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  <label for="topic">Předmět: </label>
  <input type="text" id="topic" name="topic"><br>
  <label for="date">Datum: </label>
  <input type="date" id="date" name="date"><br>
  <label for="time">Čas: </label>
  <input type="time" id="time" name="time"><br>
  <label for="attendees">Hostovali: </label>
  <input type="text" id="attendees" name="attendees"><br>
  <input type="submit">
</form>
```

Pokud váš kód používá jiný způsob pro získání aktuální události nebo pro ukládání schůzek, je třeba ho upravit podle toho, co vám funguje.


