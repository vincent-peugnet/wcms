<div id="options">
<h2>Options</h2>
<form action="./" method="get" >
<input type="submit" name="submit" value="filter">
⬅<input type="submit" name="submit" value="reset">

<div id="optfield">

<fieldset><legend>Sort</legend>
<select name="sortby" id="sortby">
<?php
foreach ($opt->col('array') as $key => $col) {
    echo '<option value="' . $col . '" ' . ($opt->sortby() == $col ? "selected" : "") . '>' . $col . '</option>';
}
?>
</select>
</br>
<input type="radio" id="asc" name="order" value="1" <?= $opt->order() == '1' ? "checked" : "" ?>/><label for="asc">ascending</label>
</br>
<input type="radio" id="desc" name="order" value="-1" <?= $opt->order() == '-1' ? "checked" : "" ?>/><label for="desc">descending</label>

</fieldset>

<fieldset><legend>Privacy</legend><ul>
<li><input type="radio" id="4" name="secure" value="4"<?= $opt->secure() == 4 ? "checked" : "" ?>/><label for="4">all</label></li>
<li><input type="radio" id="2" name="secure" value="2"<?= $opt->secure() == 2 ? "checked" : "" ?>/><label for="2">not published</label></li>
<li><input type="radio" id="1" name="secure" value="1"<?= $opt->secure() == 1 ? "checked" : "" ?>/><label for="1">private</label></li>
<li><input type="radio" id="0" name="secure" value="0"<?= $opt->secure() == 0 ? "checked" : "" ?>/><label for="0">public</label></li>
</ul></fieldset>

            <fieldset><legend>Tag</legend><ul>


<input type="radio" id="OR" name="tagcompare" value="OR" ' . <?= $opt->tagcompare() == "OR" ? "checked" : "" ?> ><label for="OR">OR</label>
<input type="radio" id="AND" name="tagcompare" value="AND" <?= $opt->tagcompare() == "AND" ? "checked" : "" ?>><label for="AND">AND</label>

<?php
$in = false;
$out = false;
$limit = 1;
foreach ($opt->taglist() as $tagfilter => $count) {

    if ($count > $limit && $in == false) {
        echo '<details open><summary>>' . $limit . '</summary>';
        $in = true;
    }
    if ($count == $limit && $in == true && $out == false) {
        echo '</details><details><summary>' . $limit . '</summary>';
        $out = true;
    }

    if (in_array($tagfilter, $opt->tagfilter())) {

        echo '<li><input type="checkbox" name="tagfilter[]" id="tag_' . $tagfilter . '" value="' . $tagfilter . '" checked /><label for="tag_' . $tagfilter . '">' . $tagfilter . ' (' . $count . ')</label></li>';
    } else {
        echo '<li><input type="checkbox" name="tagfilter[]" id="tag_' . $tagfilter . '" value="' . $tagfilter . '" /><label for="tag_' . $tagfilter . '">' . $tagfilter . ' (' . $count . ')</label></li>';
    }
}
if ($in = true || $out = true) {
    echo '</details>';
}
?>

</ul></fieldset>

        <?php
        if ($opt->invert() == 1) {
            echo '<input type="checkbox" name="invert" value="1" id="invert" checked>';
        } else {
            echo '<input type="checkbox" name="invert" value="1" id="invert">';
        }
        echo '<label for="invert">invert</></br>';
        ?>

</div>


<input type="submit" name="submit" value="filter">
⬅<input type="submit" name="submit" value="reset">

<?php if($user->isadmin()) { ?>

</form>

<h2>Columns</h2>

<form action="" method="post">

<ul>

<?php

foreach ($opt->col() as $col) {
    ?>
    <li>
    <input type="checkbox" name="col[]" id="col_<?= $col ?>">
    <label for="col_<?= $col ?>"><?= $col ?></label>
    </li>
    <?php
}

?>

</ul>

</form>

<?php } ?>

</div>
