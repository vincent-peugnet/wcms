<style>
.menu {
    all:initial;
    position: fixed;
    top: 0;
    right: 0;
    z-index: 10;
    background-color: var(--color1);
}

div#dropmenu {
    display: none;
}

.menu:hover div#dropmenu {
    display: block;
}

</style>


<div class="menu" >
    <?= $user->level() ?>
    <div id="dropmenu">

    <ul>

    <li>
    <a class="button" href="<?= $this->url('backrouter') ?>">home</a>
    </li>


<?php if($user->isvisitor()) { ?>

    <li>
    <form action="./?action=login<?= $art->id() !== null ? '&id=' . $art->id()  : '' ?>" method="post">
    <input type="password" name="pass" id="loginpass" placeholder="password">
    <input type="submit" value="login">
    </form>
    </li>

<?php } else { ?>    

    <li>
    <form action="./?action=logout<?= $art->id()  !== null ? '&id=' . $art->id()  : '' ?>" method="post">
    <input type="submit" value="logout">
    </form>
    </li>

<?php } ?>


<?php if($user->canedit()  && $artexist) { ?>

    <li>
    <a class="button" href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank">display</a>
    </li>
    <li>
    <a class="button" href="<?= $this->uart('artedit', $art->id()) ?>" >edit</a>
    </li>            

<?php } ?>    


<?php if ($user->canedit()) { ?>

    <li>
    <a class="button" href="?aff=media" >Media</a>
    </li>

<?php } ?>  

<?php if($user->isadmin()) { ?>

    <li>
    <a class="button" href="?aff=admin" >Admin</a>
    </li>

<?php } ?>  




    </div>
</div>