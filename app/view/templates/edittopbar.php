<div id="topbar">



    <form action="<?= $this->uart('artupdate', $art->id()) ?>" method="post" id="update" enctype="multipart/form-data">

    <div id="editmenu">



    <span>
    <a href="<?= $this->url('home') ?>" class="icon" >⍇</a>

    <input type="submit" value="update" accesskey="s" form="update">






    <a href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank" class="icon" >👁</a>
    <span id="headid"><?= $art->id() ?></span>
    </span>




<span id="menu" >
    <?php if($user->iseditor()) { ?>

        <a href="<?= $this->url('media') ?>"><span class="symbol">📁</span><span class="text">media</span></a>
        <a href="<?= $this->url('font') ?>"><span class="symbol">📝</span><span class="text">font</span></a>

    <?php
    if($user->isadmin()) {
    ?>
    <a href="<?= $this->url('admin') ?>"><span class="symbol">🔑</span><span class="text">admin</span></a>
    <?php
    }
    }
    ?>
</span>




<span id="fontsize">
    
    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize" min="5" max="99">
</span>

<span id="delete">
        <a href="<?= $this->uart('artconfirmdelete', $art->id()) ?>"><span class="symbol">✖</span><span class="text">delete</span></a>
</span>

</div>

</div>