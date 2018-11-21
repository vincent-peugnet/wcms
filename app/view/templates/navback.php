<div class="menu">
    <?= $user->level() ?>
    <div id="dropmenu">

    <ul>
    
    <li>
    <a class="button" href="./">home</a>
    </li>


<?php if($user->isvisitor()) { ?>

    <li>
    <form action="?action=login" method="post">
    <input type="password" name="pass" id="loginpass" placeholder="password">
    <input type="submit" value="login">
    </form>
    </li>

<?php } else { ?>    

    <li>
    <form action="?action=logout" method="post">
    <input type="submit" value="logout">
    </form>
    </li>

<?php } ?>




<?php if ($user->iseditor()) { ?>

    <li>
    <a class="button" href="?aff=media" >Media</a>
    </li>

<?php } ?>  

<?php if($user->isadmin()) { ?>

    <li>
    <a class="button" href="?aff=admin" >Admin</a>
    </li>

<?php } ?>  


    </ul>

    </div>
</div>