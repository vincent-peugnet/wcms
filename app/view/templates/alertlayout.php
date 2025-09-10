<?php $this->layout('readerlayout') ?>

<?php $this->start('head') ?>

<?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::csspath() . 'global.css" rel="stylesheet" />' : '' ?>
<meta name="viewport" content="width=device-width">
<?php if (!empty(Wcms\Config::defaultfavicon())) : ?>
    <link rel="shortcut icon" href="<?= Wcms\Model::faviconpath() . Wcms\Config::defaultfavicon() ?>" type="image/x-icon">
<?php endif ?>


<?php $this->stop() ?>



<?php $this->start('page') ?>

<style>
code {
    font-size: 1.1em;
    border: 1px solid grey;
    padding: 2px 4px;
    margin: 0 5px;
}

li {
    line-height: 26px;
    list-style: none;
}
</style>

<body class="alert">
    <main class="alert">
        <?= !empty(Wcms\Config::alerttitle()) ? '<h1>' . Wcms\Config::alerttitle() . '</h1>' : '' ?>

        <?php if (!empty($subtitle)) : ?>
            <h2>
                <?= $subtitle ?>
            </h2>
        <?php endif ?>

        <?=$this->section('alert')?>


        <?php if(!empty(Wcms\Config::alertlink())) : ?>
            <p>
                <a href="<?= $this->upage('pageread', Wcms\Config::alertlink()) ?>">
                    <?= empty(Wcms\Config::alertlinktext()) ? Wcms\Config::alertlink() : Wcms\Config::alertlinktext() ?>
                </a>
            </p>
        <?php endif ?>

    </main>
</body>


<?php $this->stop() ?>
