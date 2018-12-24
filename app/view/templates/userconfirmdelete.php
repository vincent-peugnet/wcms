
<?php
if($candelete) {
    ?>


    <h1>Delete User</h1>

    <h2>Id : <?= $userdelete->id() ?></h2>
    <h2>Level : <?= $userdelete->level() ?></h2>



    <form action="<?= $this->url('userupdate') ?>" method="post">

    <input type="hidden" name="id" value="<?= $userdelete->id() ?>">

    <input type="submit" name="action" value="confirmdelete">


    </form>





    <?php
} else {
    ?>

    <h1>You can't delete this user</h1>

    <h2>You can't delete yourself</h2>

    <p>To delete this user, create at least another admin user, log in as this other admin user, the try to delete this user.</p>

    <a href="<?= $this->url('user') ?>">Go back to users</a>


    <?php
}
?>