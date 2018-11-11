<div id="topbar">


    <form action="<?= $this->uart('artupdate', $art->id()) ?>" method="post" id="update">

    <span>
    <input type="submit" value="update" accesskey="x" form="update">
    </span>


    <span>
        <a href="<?= $this->uart('artconfirmdelete', $art->id()) ?>">✖ delete</a>
    </span>


    <span>
    <a href="<?= $this->url('backrouter') ?>" >🏠</a>
    <a href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank">👁</a>
    <a href="<?= $this->uart('artlog', $art->id()) ?>" target="_blank">¶</a>
    </span>
    <span id="headid"><?= $art->id() ?></span>

<span>

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize">
</span>
</div>