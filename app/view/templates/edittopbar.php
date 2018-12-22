<div id="topbar">



    <form action="<?= $this->uart('artupdate', $art->id()) ?>" method="post" id="update">


    <span>
    <a href="<?= $this->url('home') ?>" class="icon" >⍇</a>
    </span>
    <span>
    <input type="submit" value="update" accesskey="x" form="update">
    </span>


    <span>
        <a href="<?= $this->uart('artconfirmdelete', $art->id()) ?>"><span class="symbol">✖</span><span class="text">delete</span></a>
    </span>



    <span>
    <a href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank" class="icon" >👁</a>
    </span>
    <span id="headid"><?= $art->id() ?></span>

    <span id="fontsize">

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize" min="5" max="99">
</span>



<span id="menu" style="display: none;">
    <?php if($user->iseditor()) { ?>

    <a href="<?= $this->url('font') ?>"><span class="symbol">📝</span><span class="text">font</span></a>
    <a href="<?= $this->url('media') ?>"><span class="symbol">📁</span><span class="text">media</span></a>

    <?php
    if($user->isadmin()) {
    ?>
    <a href="<?= $this->url('admin') ?>"><span class="symbol">🔑</span><span class="text">admin</span></a>
    <?php
    }
    }
    ?>
</span>




</div>