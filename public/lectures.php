Základní logika pro splnění těchto požadavků je zapísat podmínky pro zobrazení uvedených badgů a tlačítek v souboru `public/lectures.php`. V tomto souboru by měly být následující změny:

```php
if($lecture->is_virtual == 1) {
    echo '<span style="background-color: darkgrey; color: white; padding: 5px;">ONLINE</span>';
}

if(isset($_SESSION['reservations']) && in_array($lecture->id, $_SESSION['reservations'])) {
    echo '<a href="' . $lecture-<｜begin▁of▁sentence｜>stream_url

