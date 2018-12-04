<div class="tabs">

<?php
foreach ($tablist as $key => $value) {
    echo '<div class="tab">';
    ?>

    <input name="interface" type="radio" value="<?= $key ?>" id="tab<?= $key ?>" class="checkboxtab" <?= $key == $opentab ? 'checked' : '' ?> >

    <label for="tab<?= $key ?>" <?= empty($templates[$key]) ? '' : 'title="template : '.$templates[$key].'" ' ?> class="<?= empty($templates[$key]) ? '' : 'template' ?> <?= empty($value) ? '' : 'edited' ?>"><?= $key ?> </label>

    <?php

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