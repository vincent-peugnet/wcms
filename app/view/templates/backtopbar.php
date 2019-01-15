<div id="topbar">

<span id="search">
<form action="<?= $this->url('search') ?>" method="post">
<input type="text" name="id" id="id" placeholder="page id" required>
<input type="submit" value="go">
</form>
</span>


<span id="user">

<?php if($user->isvisitor()) { ?>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="password" name="pass" id="loginpass" placeholder="password">
<input type="hidden" name="route" value="home">
<input type="submit" name="log" value="login">
</form>


<?php } else { ?>  

<span>
<a href="<?= $this->url('timeline') ?>" <?= $tab == 'timeline' ? 'class="actualpage"' : '' ?>>timeline</a>
<a href="<?= $this->url('user') ?>" <?= $tab == 'user' ? 'class="actualpage"' : '' ?>><?= $user->id() ?></a> <i><?= $user->level() ?></i>
</span>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="submit" name="log" value="logout">
</form>



</span>




<?php } ?>

<?php if($user->iseditor()) { ?>

<span id="menu">
<a href="<?= $this->url('home') ?>" <?= $tab == 'home' ? 'class="actualpage"' : '' ?>>home</a>
<a href="<?= $this->url('media') ?>" <?= $tab == 'media' ? 'class="actualpage"' : '' ?>>media</a>
<a href="<?= $this->url('font') ?>" <?= $tab == 'font' ? 'class="actualpage"' : '' ?>>font</a>
<?php
if($user->isadmin()) {
?>
<a href="<?= $this->url('admin') ?>" <?= $tab == 'admin' ? 'class="actualpage"' : '' ?>>admin</a>
<?php
}
?>
<a href="<?= $this->url('info') ?>"  <?= $tab == 'info' ? 'class="actualpage"' : '' ?>>info</a>
</span>





<?php } ?>

</div>