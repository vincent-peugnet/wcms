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

    <?php foreach ($stylesheets as $stylsheet) { ?>
        <link rel="stylesheet" href="<?= $stylsheet ?>">
    <?php } ?>
    
    <?php
    if (!empty(Wcms\Config::interfacecss())) {
        echo '<link rel="stylesheet" href="' . Wcms\Model::csspath() . Wcms\Config::interfacecss() . '">';
    }
    if (isreportingerrors()) {
    ?>
    <script>
        const sentrydsn = '<?= Wcms\Config::sentrydsn() ?>';
        const version = '<?= getversion() ?>';
        const url = '<?= Wcms\Config::url() ?>';
        const basepath = '<?= Wcms\Config::basepath() ?>';
    </script>
    <script src="https://browser.sentry-cdn.com/5.9.0/bundle.min.js"></script>
    <script src="<?= Wcms\Model::jspath() ?>sentry.bundle.js"></script>
    <?php } ?>
</head>



<?php
if (!empty($flashmessages) && is_array($flashmessages)) { ?>
<a href="#flashmessage">
    <div class="flashmessage" id="flashmessage">
        <ul>
            <?php foreach ($flashmessages as $flashmessage ) { ?>
                <li class="alert alert-<?= $flashmessage['type'] ?>">
                    <?= $flashmessage['content'] ?>
                </li>
                <?php } ?>
            </ul>
        </div>
    </a>
<?php } ?>

<body>
    <?= $this->section('page') ?>
</body>



</html>