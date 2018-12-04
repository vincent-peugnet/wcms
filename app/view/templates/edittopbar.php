<div id="topbar">



    <form action="<?= $this->uart('artupdate', $art->id()) ?>" method="post" id="update">


    <span>
    <a href="<?= $this->url('home') ?>" class="icon" >⍇</a>
    </span>
    <span>
    <input type="submit" value="update" accesskey="x" form="update">
    </span>


    <span>
        <a href="<?= $this->uart('artconfirmdelete', $art->id()) ?>">✖ delete</a>
    </span>



    <span>
    <a href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank" class="icon" >👁</a>
    <a href="<?= $this->uart('artlog', $art->id()) ?>" target="_blank" class="icon" >⁋</a>
    </span>
    <span id="headid"><?= $art->id() ?></span>

<span>

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize">
</span>



<span id="test">
    <?php if($user->iseditor()) { ?>

    <?php
    if($user->isadmin()) {
    ?>
    <a href="<?= $this->url('font') ?>">font</a>
    <a href="<?= $this->url('admin') ?>">admin</a>
    <?php
    }
    }
    ?>
</span>




</div>