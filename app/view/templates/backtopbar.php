<div id="topbar">

<span id="user">

<?php if($user->isvisitor()) { ?>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="password" name="pass" id="loginpass" placeholder="password">
<input type="submit" name="log" value="login">
</form>


<?php } else { ?>  

<span>
User level : <?= $user->level() ?> 
</span>


<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="submit" name="log" value="logout">
</form>



</span>




<?php } ?>

<?php if($user->iseditor()) { ?>

<span>
<a href="<?= $this->url('home') ?>">home</a>
<?php
if($user->isadmin()) {
?>
<a href="<?= $this->url('font') ?>">font</a>
<a href="<?= $this->url('admin') ?>">admin</a>
<?php
}
?>
| <i><a href="https://github.com/vincent-peugnet/wcms" target="_blank">github↝</a></i>
</span>





<?php } ?>

</div>