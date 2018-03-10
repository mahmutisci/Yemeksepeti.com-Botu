Example
====================
```php
include "class.yemeksepeti.php";
$restaurant = new yemeksepeti("7-70-pizza-burger", "istanbul", "levent-4");
$foods = $restaurant->getMenu();
// $comments = $restaurant->getComments($startpage, $maxpage);
```