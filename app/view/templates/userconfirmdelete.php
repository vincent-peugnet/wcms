<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'stylesheets' => [$css . 'home.css', $css . 'back.css']]) ?>

<?php $this->start('page') ?>

<?php if($candelete) : ?>

    <h1>Delete user “<?= $userdelete->id() ?>”&#8239;</h1>

    <div class="confirm-delete">

        <p>Id : <?= $userdelete->id() ?></p>
        <p>Level : <?= $userdelete->level() ?></p>

        <form action="<?= $this->url('useredit') ?>" method="post">
            <input type="hidden" name="id" value="<?= $userdelete->id() ?>">
            <input type="submit" name="action" value="Confirm delete">
        </form>

    </div>

<?php else : ?>

    <h1>You can't delete this user</h1>

    <div class="confirm-delete">
        <p>You can't delete yourself!</p>
        <p>To delete this user, create at least another admin user, log in as this other admin user, then try to delete this user.</p>
        <p><a href="<?= $this->url('user') ?>" class="button">Go back to users</a></p>
    </div>

<?php endif ?>

<?php $this->stop() ?>
