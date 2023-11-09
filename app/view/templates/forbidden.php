<?php $this->layout('layout', ['title' => 'Forbidden', 'description' => 'forbidden', 'stylesheets' => [$css . 'home.css']]) ?>




<?php $this->start('page') ?>

<h1>Forbidden</h1>

<span>
<?= $user->level() ?>
</span>

<?php if($user->isinvite()) { ?>
    <p>
        Sorry <?= $user->name() ?>, you are not allowed to do this.
    </p>
<?php } ?>

<?php
if(in_array($route, ['pageedit', 'pageread', 'pageadd'])) {
    echo '<p><a href="' . $this->upage('pageread', $id) . '">back to page read view</a></p>';
} else {
    echo '<p><a href="' . $this->url('home') . '">Go back to home</a>';
}
?>

<?php $this->stop() ?>
