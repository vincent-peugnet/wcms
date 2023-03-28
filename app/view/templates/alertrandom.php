<?php $this->layout('alertlayout', ['subtitle' => '']) ?>




<?php $this->start('alert') ?>


    <?= !empty(Wcms\Config::alerttitle()) ? '<h1>' . Wcms\Config::alerttitle() . '</h1>' : '' ?>

    <h2>Random page tool error</h2>

    <p><?= $message ?></p>

<?php $this->stop() ?>
