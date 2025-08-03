<?php $this->layout('backlayout', ['title' => 'Connect', 'description' => 'connect', 'stylesheets' => [$css . 'back.css', $css . 'connect.css'], 'theme' => $theme]) ?>


<?php $this->start('page') ?>

<div class="connect">
    <h2>Login</h2>

    <?php if($user->isvisitor()) : ?>

        <form action="<?= $this->url('log') ?>" method="post">
            <input type="hidden" name="route" value="<?= $route ?>">
            <?php if(in_array($route, ['pageedit', 'pageread', 'pageadd'])) : ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif ?>
            <input type="text" name="user" id="loginuser" autofocus placeholder="user" required>
            <input type="password" name="pass" id="loginpass" placeholder="password" required>
            <input type="hidden" name="rememberme" value="0">
            <span>
                <input type="checkbox" name="rememberme" id="rememberme" value="1">
                <label for="rememberme">Remember me</label>
            </span>
            <button type="submit" name="log" value="login">
                <i class="fa fa-sign-in"></i>
                <span>login</span>
            </button>
        </form>

    <?php else : ?>    

        <form action="<?= $this->url('log') ?>" method="post">
            <input name="log" type="submit" value="logout">
        </form>

    <?php endif ?>

    <?php if(in_array($route, ['pageedit', 'pageread', 'pageadd'])) : ?>
        <p>
            <a class="button" href="<?= $this->upage('pageread', $id) ?>">
            <i class="fa fa-eye"></i>
            display page
        </a>
    </p>
    <?php endif ?>

    <?php if (!empty($helpbutton)) : ?>
        <a href="<?= $helpbutton ?>" class="button">
            <i class="fa fa-info-circle"></i>
            help
        </a>
    <?php endif ?>
</div>

<?php $this->stop() ?>
