<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8" />
   
    <meta name="viewport" content="width=device-width" />
    <?php if(!empty($favicon)) {
        ?>
        <link rel="shortcut icon" href="<?= Model::faviconpath() . $favicon ?>" type="image/x-icon">
    <?php } elseif(!empty(Config::defaultfavicon())) { ?>
        <link rel="shortcut icon" href="<?= Model::faviconpath() . Config::defaultfavicon() ?>" type="image/x-icon">
    <?php } ?>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $css ?>">
    <script>
        // global js vars
        const basepath = '/<?= Config::basepath() ?>/';
        const jspath = '<?= Model::jspath() ?>';
    </script>
</head>



<?=$this->section('page')?>


</html>