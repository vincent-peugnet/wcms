<?php $this->layout('modallayout', ['title' => 'Forbidden', 'description' => 'forbidden', 'css' => $css, 'user' => $user, 'pagelist' => $pagelist]) ?>


<?php $this->start('modal') ?>

<?php if($user->isinvite()) : ?>
    <p>
        Sorry <?= $this->e($user->name()) ?>, you are not allowed to do this.
    </p>
<?php endif ?>

<?php if(in_array($route, ['pageedit', 'pageread', 'pageadd', 'pagedownload'])) :?>
    <p><a href="<?= $this->upage('pageread', $id) ?>" class="button">back to page read view</a></p>
<?php else : ?>
    <p><a href="<?= $this->url($route) ?>" class="button">Go back</a>
<?php endif ?>

<?php $this->stop() ?>
