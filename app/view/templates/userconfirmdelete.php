<?php $id = $userdelete->id() ?>
<?php $this->layout('modallayout', ['title' => "Delete user $id", 'description' => 'delete', 'css' => $css, 'user' => $user, 'pagelist' => $pagelist]) ?>

<?php $this->start('modal') ?>

<?php if($candelete) : ?>


    <p>Id : <?= $userdelete->id() ?></p>
    <p>Level : <?= $userdelete->level() ?></p>

    <form action="<?= $this->url('useredit') ?>" method="post">
        <input type="hidden" name="id" value="<?= $userdelete->id() ?>">
        <input type="submit" name="action" value="Confirm delete">
    </form>


<?php else : ?>

    <p>You can't delete yourself!</p>
    <p>To delete this user, create at least another admin user, log in as this other admin user, then try to delete this user.</p>
    <p><a href="<?= $this->url('user') ?>" class="button">Go back to users</a></p>


<?php endif ?>

<?php $this->stop() ?>
