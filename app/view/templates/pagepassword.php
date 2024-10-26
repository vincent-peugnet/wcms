<?php

$this->layout('readerlayout') ?>

<?php $this->start('head'); ?>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>🔑</title>

    <?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::dirtopath(Wcms\Model::ASSETS_CSS_DIR) . 'global.css" rel="stylesheet" />' : '' ?>
    <meta name="viewport" content="width=device-width">

<?php $this->stop(); ?>

<?php $this->start('page') ?>

    <body class="alert">

        <main class="alert">

            <h1>This page is password protected</h1>

            <form action="<?= $this->url('pagereadpost', ['page' => $pageid]) ?>" method="post">
                <label for="pagepassword">Page password</label>
                <input type="password" name="pagepassword" id="pagepassword" autofocus required>
                <input type="submit" value="OK">
            </form>

        </main>

    </body>

<?php $this->stop() ?>
