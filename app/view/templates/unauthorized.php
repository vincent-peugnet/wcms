<?php $this->layout('layout', ['title' => 'Unauthorized', 'description' => 'unauthorized', 'stylesheets' => [$css . 'home.css']]) ?>




<?php $this->start('page') ?>

<h1>Unauthorized</h1>

<span>
<?= $user->level() ?>
</span>

<?php
if(in_array($route, ['pageedit', 'pageread', 'pageadd'])) {
    echo '<p><a href="' . $this->upage('pageread', $id) . '">back to page read view</a></p>';
}
?>

<?php $this->stop() ?>
