<?php $this->layout('layout', ['title' => 'Connect', 'description' => 'connect', 'css' => $css . 'connect.css']) ?>




<?php $this->start('page') ?>

<span>
<?= $user->level() ?>
</span>

<?php if($user->isvisitor()) { ?>

<?= $route === 'artedit' ? '<p>Your edits have been temporary saved. You need to connect and update to store it completly</p>' : '' ?>

<form action="<?= $this->url('log') ?>" method="post">
<input type="hidden" name="route" value="<?= $route ?>">
<?php
if(in_array($route, ['artedit', 'artread', 'artread/'])) {
    echo '<input type="hidden" name="id" value="'. $id .'">';
}
?>
<input type="password" name="pass" id="loginpass" placeholder="password">
<input name="log" type="submit" value="login">
</form>


<?php } else { ?>    

<form action="<?= $this->url('log') ?>" method="post">
<input name="log" type="submit" value="logout">
</form>



<?php } ?>

<?php $this->stop() ?>