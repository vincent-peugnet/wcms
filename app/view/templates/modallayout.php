<?php $this->layout('backlayout', ['title' => $title, 'description' => $description, 'stylesheets' => [$css . 'modal.css']]) ?>

<?php $this->start('page') ?>


<?php $this->insert('backtopbar', ['user' => $user, 'pagelist' => $pagelist, 'tab' => null]) ?>

<main>
    <div class="modal">

        <h2><?= $title ?></h2>
        <?= $this->section('modal') ?>

    </div>
</main>

<?php $this->stop() ?>
