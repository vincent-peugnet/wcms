<?php

$this->layout('readerlayout') ?>

<?php
$this->start('head');
?>

<head>
    <?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::dirtopath(Wcms\Model::ASSETS_CSS_DIR) . 'global.css" rel="stylesheet" />' : '' ?>
    <meta name="viewport" content="width=device-width">
</head>


<?php
$this->stop();
?>



<?php $this->start('page') ?>

<body class="alert">

<main class="alert">


<h1>This page is password protected</h1>

<form action="<?= $this->url('pagereadpost', ['page' => $pageid]) ?>" method="post">
<label for="pagepassword">Page password</label>
<input type="password" name="pagepassword" id="pagepassword" autofocus required>
</form>

</main>


</body>

<?php $this->stop() ?>
