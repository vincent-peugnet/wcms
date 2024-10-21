<?php $this->layout('layout', ['title' => 'Forbidden', 'description' => 'forbidden', 'stylesheets' => [$css . 'home.css']]) ?>




<?php $this->start('page') ?>


<?php $this->insert('backtopbar', ['user' => $user, 'pagelist' => $pagelist]) ?>

<h1>Forbidden</h1>

<span>
<?= $this->e($user->level()) ?>
</span>

<?php if($user->isinvite()) : ?>
    <p>
        Sorry <?= $this->e($user->name()) ?>, you are not allowed to do this.
    </p>
<?php endif ?>

<?php if(in_array($route, ['pageedit', 'pageread', 'pageadd', 'pagedownload'])) :?>
    <p><a href="<?= $this->upage('pageread', $id) ?>">back to page read view</a></p>
<?php else : ?>
    <p><a href="<?= $this->url('home') ?>">Go back to home</a>
<?php endif ?>

<?php $this->stop() ?>
