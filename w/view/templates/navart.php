<div class="menu">
    <?= $user->level() ?>
    <div id="dropmenu">

    <ul>

    <li>
    <a class="button" href="./">home</a>
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
    <a class="button" href="?id=<?=$art->id() ?>" target="_blank">display</a>
    </li>
    <li>
    <a class="button" href="?id=<?=$art->id() ?>&aff=edit" >edit</a>
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