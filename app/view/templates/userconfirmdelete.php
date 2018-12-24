

<h1>Delete User</h1>

<h2>Id : <?= $userdelete->id() ?></h2>
<h2>Level : <?= $userdelete->level() ?></h2>



<form action="<?= $this->url('userupdate') ?>" method="post">

<input type="hidden" name="id" value="<?= $userdelete->id() ?>">

<input type="submit" name="action" value="confirmdelete">


</form>