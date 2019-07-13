<div id="edittopbar">



    <form action="<?= $this->uart('artupdate', $art->id()) ?>" method="post" id="update" enctype="multipart/form-data">

    <div id="editmenu">



    <span>

    <input type="submit" value="update" accesskey="s" form="update">






    <a href="<?= $this->uart('artread/', $art->id()) ?>" target="_blank" ><img src="<?= Model::iconpath() ?>read.png" class="icon">display</a>
    <span id="headid"><?= $art->id() ?></span>
    </span>

<span id="fontsize">
    
    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize" min="5" max="99">
</span>

<span id="download">
        <a href="<?= $this->uart('artdownload', $art->id()) ?>"><img src="<?= Model::iconpath() ?>download.png" class="icon"><span class="text">download</span></a>
</span>


<span id="delete">
        <a href="<?= $this->uart('artconfirmdelete', $art->id()) ?>"><span class="symbol">✖</span><span class="text">delete</span></a>
</span>

</div>

</div>