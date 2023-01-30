<?php

$this->layout('readerlayout') ?>

<?php
$this->start('head');
?>

<?= Wcms\Config::alertcss() ? '<link href="' . Wcms\Model::dirtopath(Wcms\Model::ASSETS_CSS_DIR) . 'global.css" rel="stylesheet" />' : '' ?>
<meta name="viewport" content="width=device-width">


<?php
$this->stop();
?>



<?php $this->start('page') ?>

<body class="alert">

<main class="alert">






    <?= !empty(Wcms\Config::alerttitle()) ? '<h1>' . Wcms\Config::alerttitle() . '</h1>' : '' ?>

    <h2>Random page tool error</h2>

    <p><?= $message ?></p>




</main>


</body>

<?php $this->stop() ?>
