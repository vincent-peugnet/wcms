<div id="topbar">

<?php if($user->isvisitor()) { ?>


<form action="<?= $this->url('log') ?>" method="post">
<input type="password" name="pass" id="loginpass" placeholder="password">
<input type="submit" name="log" value="login">
</form>


<?php } else { ?>    

<form action="<?= $this->url('log') ?>" method="post">
<input type="submit" name="log" value="logout">
</form>

<span>
User level : <?= $user->level() ?> 
</span>

<?php } ?>

<?php if($user->iseditor()) { ?>


<span>
| <a href="">media</a>
| <a href="">admin</a>
</span>



<?php } ?>

</div>