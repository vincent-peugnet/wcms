<html>
<a href="/w/">w</a>
</html>

<?php

var_dump(__FILE__);

$htdocs = str_replace(basename(__FILE__), __FILE__, '');

var_dump($htdocs);

var_dump($_SERVER["DOCUMENT_ROOT"]);


set_include_path('e:/WEB/wcms');

echo get_include_path();



include('fn/fn.php');


?>