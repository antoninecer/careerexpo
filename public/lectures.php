```php
<?php
require_once 'functions.php';

$currentEventId = getCurrentEventId();

$lectures = requireEvent($currentEventId);

foreach ($lectures as $lecture) {
    echo "Lecture: " . $lecture->title . "\n";
    echo "Speaker: " . $lecture->speaker . "\n";
    echo "Date: " . $lecture->date . "\n";
    echo "Time: " . $lecture->time . "\n";
    echo "-----------------------------------------\n";
}
?>
```

V tomto příkladě předpokládáme, že existuje funkce `getCurrentEventId()` a `requireEvent($eventId)`. Funkce `getCurrentEventId()` by měla získat id aktuálního eventu a funkce `requireEvent($eventId)` by měla získat informace o přednáškách pro daný event.


