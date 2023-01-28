<?php
if (preg_match('%^/(assets|media)/%', $_SERVER["REQUEST_URI"]) && is_file($_SERVER["SCRIPT_FILENAME"])) {
    return false;    // serve the requested resource as-is.
} else { 
    include('index.php');
}
?>
