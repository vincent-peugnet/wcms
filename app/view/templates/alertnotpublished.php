<?php $this->layout('alertlayout', ['subtitle' => $subtitle]) ?>

<?php $this->start('alert') ?>


<?php if ($user->isvisitor() && Wcms\Config::notpublishedpass())  : ?>

    <p>
        <?php $this->insert('alertform', ['id' => $page->id()]) ?>
    </p>

<?php endif ?>

<?php $this->stop() ?>

