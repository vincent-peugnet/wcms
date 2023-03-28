<form action="<?= $this->url('log') ?>" method="post">
    <input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
    <input type="password" name="pass" id="loginpass" placeholder="password" required>
    <input type="hidden" name="route" value="pageread">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="checkbox" name="rememberme" id="rememberme" value="1">
    <label for="rememberme">Remember me</label>
    <input type="submit" name="log" value="login" id="button">
</form>
