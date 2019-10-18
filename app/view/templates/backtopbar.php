<header id="topbar">

<span id="search">
<form action="<?= $this->url('search') ?>" method="post">
<input type="text" list="searchdatalist" name="id" id="search" placeholder="page id" required <?= $tab !== 'edit' && !$user->isvisitor() ? 'autofocus' : '' ?>>
<input type="submit" name="action" value="read">
<?= $user->iseditor() ? '<input type="submit" name="action" value="edit">' : '' ?>

<?php if($user->iseditor()) { ?>
<datalist id="searchdatalist">
    <?php foreach ($pagelist as $id) { ?>
        <option value="<?= $id ?>"><?= $id ?></option>
    <?php } ?>
</datalist>
<?php } ?>

</form>
</span>



<?php if($user->iseditor()) { ?>

<span id="menu">
<a href="<?= $this->url('home') ?>" <?= $tab == 'home' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>home.png" alt="" class="icon">home</a>
<a href="<?= $this->url('media') ?>" <?= $tab == 'media' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>media.png" alt="" class="icon">media</a>
<a href="<?= $this->url('font') ?>" <?= $tab == 'font' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>font.png" alt="" class="icon">font</a>
<?php
if($user->isadmin()) {
?>
<a href="<?= $this->url('admin') ?>" <?= $tab == 'admin' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>admin.png" alt="" class="icon">admin</a>
<?php
}
?>
<a href="<?= $this->url('info') ?>"  <?= $tab == 'info' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>info.png" alt="" class="icon">info</a>
</span>





<?php } ?>



<span id="user">

<?php if($user->isvisitor()) { ?>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="password" name="pass" id="loginpass" placeholder="password" autofocus>
<input type="hidden" name="route" value="home">
<input type="submit" name="log" value="login">
</form>


<?php } else { ?>  

<span>
<a href="<?= $this->url('timeline') ?>" <?= $tab == 'timeline' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>timeline.png" alt="" class="icon">timeline</a>
<a href="<?= $this->url('user') ?>" <?= $tab == 'user' ? 'class="actualpage"' : '' ?>><img src="<?= Model::iconpath() ?>user.png" alt="" class="icon"><?= $user->id() ?></a> <i><?= $user->level() ?></i>
</span>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="submit" name="log" value="logout" >
<?php if($tab === 'edit') { ?>
    <input type="hidden" name="route" value="pageread/">
    <input type="hidden" name="id" value="<?= $pageid ?>">
<?php } ?>

</form>



</span>




<?php } ?>

</header>