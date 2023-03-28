<?php $this->layout('alertlayout', ['subtitle' => $subtitle]) ?>

<?php $this->start('alert') ?>

<?php if ($user->isvisitor() && Wcms\Config::existnotpass())  { ?>

    <p>
        <?php $this->insert('alertform', ['id' => $page->id()]) ?>
    </p>

<?php } ?>



<?php if ($canedit) { ?>
    <p>
        <a href="<?= $this->upage('pageadd', $page->id()) ?>">⭐ Create</a>
    </p>

    <p>
        💡 To create a page in one command, you can type
        <code><?= $this->upage('pageadd', $page->id()) ?></code>
        directly in your address bar.
    </p>
<?php } ?>








<?php $this->stop() ?>

