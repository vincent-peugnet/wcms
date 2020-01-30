<div id="edittopbar">



    <form action="<?= $this->upage('pageupdate', $page->id()) ?>" method="post" id="update">

    <div id="editmenu">



    <span>

    <input type="submit" value="update" accesskey="s" form="update">






    <a href="<?= $this->upage('pageread/', $page->id()) ?>" target="_blank" id="display">
        <img src="<?= Wcms\Model::iconpath() ?>read.png" class="icon">
        <span class="hidephone">display</span>
    </a>
    <span id="headid"><?= $page->id() ?></span>
    </span>

<span id="fontsize" class="hidephone">
    
    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Wcms\Config::fontsize() ?>" id="editfontsize" min="5" max="99">
</span>

<span id="download" class="hidephone">
        <a href="<?= $this->upage('pagedownload', $page->id()) ?>"><img src="<?= Wcms\Model::iconpath() ?>download.png" class="icon"><span class="text">download</span></a>
</span>


<span id="delete" class="hidephone">
        <a href="<?= $this->upage('pageconfirmdelete', $page->id()) ?>"><span class="symbol">✖</span><span class="text">delete</span></a>
</span>

</div>

</div>