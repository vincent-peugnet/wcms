<?php


foreach ($diff as $element) {
    echo '<h2>' . $element . '</h2>';
    echo '<textarea style="width: 100%; max-width: 600px; height: 300px;">' . $mergeart->$element() . '</textarea>';
}

?>