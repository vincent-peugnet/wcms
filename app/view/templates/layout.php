<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width" />
    <?php if (!empty($favicon)) : ?>
        <link rel="shortcut icon" href="<?= Wcms\Model::faviconpath() . $favicon ?>" type="image/x-icon">
    <?php elseif (!empty(Wcms\Config::defaultfavicon())) : ?>
        <link rel="shortcut icon" href="<?= Wcms\Model::faviconpath() . Wcms\Config::defaultfavicon() ?>" type="image/x-icon">
    <?php endif ?>
    <title><?= $title ?></title>

    <link rel="stylesheet" href="<?= Wcms\Model::assetscsspath() ?>base.css">
    <link rel="stylesheet" href="<?= Wcms\Model::assetscsspath() ?>fork-awesome.css">
    <?php foreach ($stylesheets as $stylsheet) : ?>
        <link rel="stylesheet" href="<?= $stylsheet ?>">
    <?php endforeach ?>
    
    <?php if (!empty(Wcms\Config::theme())) : ?>
        <link rel="stylesheet" href="<?= Wcms\Model::themepath() . Wcms\Config::theme() ?>">
    <?php endif ?>

    <?php if (isreportingerrors()) : ?>
    <script>
        const sentrydsn = '<?= Wcms\Config::sentrydsn() ?>';
        const version = '<?= getversion() ?>';
        const url = '<?= Wcms\Config::url() ?>';
        const basepath = '<?= Wcms\Config::basepath() ?>';
    </script>
    <script type="module" src="<?= Wcms\Model::jspath() ?>sentry.bundle.js"></script>
    <?php endif ?>
</head>



<?php
if (!empty($flashmessages) && is_array($flashmessages)) : ?>
<a href="#flashmessage">
    <div class="flashmessage" id="flashmessage">
        <ul>
            <?php foreach ($flashmessages as $flashmessage ) : ?>
                <li class="alert alert-<?= $flashmessage['type'] ?>">
                    <?= $flashmessage['content'] ?>
                </li>
            <?php endforeach ?>
            </ul>
        </div>
    </a>
<?php endif ?>

<body>
    <?= $this->section('page') ?>
</body>



</html>
