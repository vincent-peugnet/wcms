<?php


require('../w/class/class.w.quickcss.php');

$quick = new Quickcss;



var_dump($quick);




$color = ['color', 'background-color', 'border-color'];
$size = ['width', 'height', 'margin', 'padding', 'border-width'];
$font = ['font-size'];
$unique = ['background-image', 'opacity', 'border-style', 'text-align'];
$params = array_merge($color, $size, $font, $unique);






// $presets = ['body' => ['font-size' => 'px']];
// $quickcss = ['section' => ['color' => '#a74545', 'font-size' => '32px'], 'p' => ['background-color' => '#458da7', 'width' => '7px']];

$presets = [];
$quickcss = [];

if (isset($_POST['quickcss'])) {
    $quickcss = $_POST['quickcss'];
}

if (isset($_POST['presets'])) {
    $presets = $_POST['presets'];
}


if (isset($_POST['active'])) {
    $active = $_POST['active'];
    echo '<h3>active</h3>';
    var_dump($active);
    $intersect = array_intersect_key($quickcss, $active);

    foreach ($intersect as $element => $css) {
        $intersect[$element] = array_intersect_key($quickcss[$element], $active[$element]);
    }
    
    echo '<h3>intersect</h3>';
    var_dump($intersect);

    $quickcss = $intersect;


}


if (!empty($_POST['new']['element']) && !empty($_POST['new']['param']) && in_array($_POST['new']['param'], $params)) {
    $new = array($_POST['new']['element'] => array($_POST['new']['param'] => ''));
    var_dump($new);

    $quickcss = array_merge_recursive($quickcss, $new);
}




echo '<h3>quickcss</h3>';

var_dump($quickcss);

echo '<h3>presets</h3>';

var_dump($presets);

foreach ($presets as $element => $preset) {
    foreach ($preset as $param => $unit) {
        if (array_key_exists($element, $quickcss) && array_key_exists($param, $quickcss[$element])) {
            $quickcss[$element][$param] .= $unit;
        }
    }
}


$jsonquickcss = json_encode($quickcss);

var_dump($jsonquickcss);

$string = '';
foreach ($quickcss as $key => $css) {
    $string .= PHP_EOL . $key . ' {';
    foreach ($css as $param => $value) {
 
            $string .= PHP_EOL . '    ' . $param . ': ' . $value . ';';

    }
    $string .= PHP_EOL . '}' . PHP_EOL;
}

var_dump($string);




echo '<form action="test.php" method="post">';

foreach ($quickcss as $element => $css) {
    echo '<h3>' . $element . '</h3>';
    foreach ($css as $param => $value) {

        echo '<input type="checkbox" name="active[' . $element . '][' . $param . ']" checked>';

        if (in_array($param, $color)) {
            echo '<label for="quickcss[' . $element . '][' . $param . ']">' . $param . '</label>';
            echo '<input type="color" name="quickcss[' . $element . '][' . $param . ']" value="' . $quickcss[$element][$param] . '" id="quickcss[' . $element . '][' . $param . ']">';
        }

        if (in_array($param, $size)) {
            echo '<label for="quickcss[' . $element . '][' . $param . ']">' . $param . '</label>';
            echo '<input type="number" name="quickcss[' . $element . '][' . $param . ']" value="' . intval($quickcss[$element][$param]) . '" id="quickcss[' . $element . '][' . $param . ']">';

            $unit = preg_replace('/\d/', '', $quickcss[$element][$param]);
            ?>
            <select name="presets[<?= $element ?>][<?= $param ?>]" >
                <option value="px" <?= $unit == 'px' ? 'selected' : '' ?>>px</option>
                <option value="%" <?= $unit == '%' ? 'selected' : '' ?>>%</option>
            </select>
            <?php

        }

        if (in_array($param, $font)) {
            echo '<label for="quickcss[' . $element . '][' . $param . ']">' . $param . '</label>';
            echo '<input type="number" name="quickcss[' . $element . '][' . $param . ']" value="' . intval($quickcss[$element][$param]) . '" id="quickcss[' . $element . '][' . $param . ']">';

            $unit = preg_replace('/\d/', '', $quickcss[$element][$param]);
            ?>
            <select name="presets[<?= $element ?>][<?= $param ?>]" >
                <option value="px" <?= $unit == 'px' ? 'selected' : '' ?>>px</option>
                <option value="em" <?= $unit == 'em' ? 'selected' : '' ?>>em</option>
            </select>
            <?php

        }
    }
}

echo '<h1>Add element</h1>';

echo '<input type="text" name="new[element]">';
echo '<select name="new[param]">';
foreach ($params as $param) {
    echo '<option value="' . $param . '">' . $param . '</option>';
}
echo '</select>';

echo '</br><input type="submit" value="submit">';

echo '</form>';

?>




<style>

<?= $string ?>



</style>

<section>
    <h4>Yolo babyzesssssss</h4>

    <p>
    Note that the values of array need to be valid keys, i.e. they need to be either integer or string. A warning will be emitted if a value has the wrong type, and the key/value pair in question will not be included in the result.

If a value has several occurrences, the latest key will be used as its value, and all others will be lost.
    </p>


</section>