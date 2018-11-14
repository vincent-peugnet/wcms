<div class="tabs">

<?php
foreach ($tablist as $key => $value) {
    echo '<div class="tab">';
    if ($key == $opentab) {
        echo '<input name="interface" type="radio" value="'.$key.'" id="tab' . $key . '" class="checkboxtab" checked>';
    } else {
        echo '<input name="interface" type="radio" value="'.$key.'" id="tab' . $key . '" class="checkboxtab">';
    }
    echo '<label for="tab' . $key . '">' . $key . '</label>';
    echo '<div class="content">';
    if ($key == $opentab) {
        echo '<textarea name="' . $key . '" id="' . $key . '"  autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus >' . $value . '</textarea>';
    } else {
        echo '<textarea name="' . $key . '" id="' . $key . '"  autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">' . $value . '</textarea>';
    }
    echo '</div>';
    echo '</div>';
}
?>

</div>