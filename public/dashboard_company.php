Základní vzorový kód, který bude vypadat takto, můžeme vypadat takto:

```php
<?php
require('functions.php'); // připojení souboru s funkcemi

$currentEventId = getCurrentEventId(); // získání aktuálního event_id

$event = requireEvent($currentEventId); // načtení aktuálního eventu

// filtrování spojení, úspěchy a žádostí podle event_id
$connections = filterConnectionsByEventId($currentEventId);
$successes = filterSuccessesByEventId($currentEventId);
$requests = filterRequestsByEventId($currentEventId);

// filtrování pozic podle event_id
$jobs = filterJobsByEventId($currentEventId);

// výpis data
echo "Event ID: " . $event['event_id'] . "<br>";
echo "Event Name: " . $event['event_name'] . "<br>";
echo "Number of Connections: " . count($connections) . "<br>";
echo "Number of Successes: " . count($successes) . "<br>";
echo "Number of Requests: " . count($requests) . "<br>";
echo "Number of Jobs: " . count($jobs) . "<br>";

?>
```

Všimněte si, že v tomto příkladu předpokládáme, že existují tři funkce (`filterConnectionsByEventId`, `filterSuccessesByEventId` a `filterRequestsByEventId`), které filtrovají spojení, úspěchy a žádosti podle event_id. Také předpokládáme, že existuje i funkce `filterJobsByEventId`, která filtrování pozic podle event_id.

Pokud tyto funkce neexistují, je třeba jim je definovat nebo je přidat do souboru `functions.php`, který by měl být v kořenovém adresáři vašeho projektu.

Tento kód je pouze základní vzor, který byste měli upravit na základě vámi existujících funkcí a konfigurace.


