<?php $this->layout('layout', ['title' => 'Connect', 'description' => 'connect', 'css' => $css . 'connect.css']) ?>




<?php $this->start('page') ?>

<span>
<?= $user->level() ?>
</span>

<?php if($user->isvisitor()) { ?>

<form action="./?action=login" method="post">
<input type="password" name="pass" id="loginpass" placeholder="password">
<input type="submit" value="login">
</form>


<?php } else { ?>    

<form action="./?action=logout" method="post">
<input type="submit" value="logout">
</form>



<?php } ?>

<?php $this->stop() ?>