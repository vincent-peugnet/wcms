<?php $this->layout('layout', ['title' => 'Connect', 'description' => 'connect', 'stylesheets' => [$css . 'home.css']]) ?>




<?php $this->start('page') ?>

<span>
<?= $user->level() ?>
</span>

<?php if($user->isvisitor()) { ?>

<form action="<?= $this->url('log') ?>" method="post">
<input type="hidden" name="route" value="<?= $route ?>">
<?php
if(in_array($route, ['pageedit', 'pageread', 'pageread/', 'pageadd'])) {
    echo '<input type="hidden" name="id" value="'. $id .'">';
}
?>
<form action="<?= $this->url('log') ?>" method="post" id="connect">
<input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
<input type="password" name="pass" id="loginpass" placeholder="password" required>
<input type="hidden" name="rememberme" value="0">
<input type="checkbox" name="rememberme" id="rememberme" value="1">
<label for="rememberme">Remember me</label>
<input name="log" type="submit" value="login">
</form>


<?php } else { ?>    

<form action="<?= $this->url('log') ?>" method="post">
<input name="log" type="submit" value="logout">
</form>



<?php } ?>

<?php
if(in_array($route, ['pageedit', 'pageread', 'pageread/', 'pageadd'])) {
    echo '<p><a href="' . $this->upage('pageread/', $id) . '">back to page read view</a></p>';
}
?>

<?php $this->stop() ?>
