<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8" />

    <meta name="viewport" content="width=device-width" />
    <?php if (!empty($favicon)) {
        ?>
        <link rel="shortcut icon" href="<?= Wcms\Model::faviconpath() . $favicon ?>" type="image/x-icon">
    <?php } elseif (!empty(Wcms\Config::defaultfavicon())) { ?>
        <link rel="shortcut icon" href="<?= Wcms\Model::faviconpath() . Wcms\Config::defaultfavicon() ?>" type="image/x-icon">
    <?php } ?>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= $css ?>">
    <?php
    if (!empty(Wcms\Config::interfacecss())) {
        echo '<link rel="stylesheet" href="' . Wcms\Model::csspath() . Wcms\Config::interfacecss() . '">';
    }
    if (!empty(Wcms\Config::sentrydsn())) {
    ?>
    <script>
        const sentrydsn = '<?= Wcms\Config::sentrydsn() ?>';
        const version = '<?= getversion() ?>';
        const url = '<?= Wcms\Config::url() ?>';
        const basepath = '<?= Wcms\Config::basepath() ?>';
    </script>
    <script src="<?= Wcms\Model::jspath() ?>sentry.bundle.js"></script>
    <?php } ?>
</head>



<?= $this->section('page') ?>


</html>