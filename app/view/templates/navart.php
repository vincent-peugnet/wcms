<style>
.wqn, .wqn *, .wqn:hover *, .wqn *:hover{
    all:initial;

}
.wqn {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 10;
    background-color: var(--color1);
}

.wqn div#dropwqn {
    background-color: lightgrey;
    text-align: right;
    font-family: monospace;
}

.wqn li.drop{
    display: block;
    text-align: right;
}

.wqn li.drop a.button:hover, .wqn li.drop input[type="submit"]:hover {
    color: white;
}

.wqn li.drop a.button, .wqn li.drop input[type="submit"] {
    cursor: pointer;
    font-family: monospace;
    font-size: 15px;
}

.wqn li.drop a.button, .wqn li.drop input[type="submit"] {
    cursor: pointer;
    font-family: monospace;
    font-size: 15px;
}

.wqn div#dropwqn {
    display: none;
}

.wqn input#loginpass {
    width: 70px;
    background-color: white;
}

.wqn:hover div#dropwqn {
    display: block;
}

</style>


<div class="wqn" >
<div style="opacity: 0.5; text-align: right; display: block;">✎</div>
    <div id="dropwqn">

    <ul>
    <li class="drop">
    <span class="button" style="font-family: monospace; background-color: #7b97b9;" ><?= $user->id() ?> (<?= $user->level() ?>)</span>
    </li>
    <li class="drop">
    <a class="button"  href="<?= $this->url('home') ?>">home</a>
    </li>


<?php if($user->isvisitor()) { ?>

    <li class="drop">
    <form action="<?= $this->url('log') ?>" method="post">
    <input type="password" name="pass" id="loginpass" placeholder="password">
    <input type="hidden" name="route" value="artread/">
    <input type="hidden" name="id" value="<?= $art->id() ?>">
    <input type="submit" name="log" value="login" id="button">
    </form>
    </li>

<?php } else { ?>    

    <li class="drop">
    <form action="<?= $this->url('log') ?>" method="post">
    <input type="hidden" name="id" value="<?= $art->id() ?>">
    <input type="hidden" name="route" value="artread/">
    <input type="submit" name="log" value="logout" id="button">
    </form>
    </li>

<?php } ?>


<?php if($canedit  && $artexist) { ?>

    <li class="drop">
    <a class="button" href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank">display</a>
    </li>
    <li class="drop">
    <a class="button" href="<?= $this->uart('artedit', $art->id()) ?>" >edit</a>
    </li>            
    <li class="drop">
    <a class="button" href="<?= $this->uart('artrender', $art->id()) ?>" >render</a>
    </li>            

<?php } ?>    


<?php if ($user->iseditor()) { ?>

    <li class="drop">
    <a class="button" href="<?= $this->url('media') ?>" >Media</a>
    </li>

<?php } ?>  

<?php if($user->isadmin()) { ?>

    <li class="drop">
    <a class="button" href="<?= $this->url('admin') ?>" >Admin</a>
    </li>

<?php } ?>  




    </div>
</div>