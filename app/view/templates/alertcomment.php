<?php $this->layout('alertlayout', ['subtitle' => 'Comment error']) ?>

<?php $this->start('alert') ?>

<p>
    <?= $message ?>
</p>


<?php if ($user->iseditor()) : ?>
    <p>
        <a href="<?= $this->url('home') ?>">🏠 Go back to home</a>
    </p>
<?php endif ?>


<?php $this->stop() ?>
