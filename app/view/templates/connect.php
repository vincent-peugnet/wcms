<?php $this->layout('layout', ['title' => 'Connect', 'description' => 'connect', 'css' => $css . 'connect.css']) ?>




<?php $this->start('page') ?>

<span>
<?= $user->level() ?>
</span>

<?php if($user->isvisitor()) { ?>

<form action="<?= $this->url('log') ?>" method="post">
<input type="password" name="pass" id="loginpass" placeholder="password">
<input name="log" type="submit" value="login">
</form>


<?php } else { ?>    

<form action="<?= $this->url('log') ?>" method="post">
<input name="log" type="submit" value="logout">
</form>



<?php } ?>

<?php $this->stop() ?>