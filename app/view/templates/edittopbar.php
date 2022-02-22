<div id="edittopbar">



    <form
        action="<?= $this->upage('pageupdate', $page->id()) ?>"
        method="post"
        id="update"
        data-api="<?= $this->upage('apipageupdate', $page->id()) ?>"
    >

    <div id="editmenu">



        <span>

        <input type="submit" value="update" accesskey="s" form="update">






        <a href="<?= $this->upage('pageread/', $page->id()) ?>" target="<?= $page->id() ?>" id="display">
            <i class="fa fa-eye"></i>
            <span>display</span>
        </a>
        <span id="headid"><?= $page->id() ?></span>
        </span>

        <span id="fontsize">
            
            <label for="fontsize">Font-size</label>
            <input type="number" name="fontsize" value="<?= Wcms\Config::fontsize() ?>" id="editfontsize" min="5" max="99">
        </span>

        <span id="download">
                <a href="<?= $this->upage('pagedownload', $page->id()) ?>">
                    <i class="fa fa-download"></i>
                    <span class="text">download</span>
                </a>
        </span>


        <span id="delete">
                <a href="<?= $this->upage('pageconfirmdelete', $page->id()) ?>">
                    <i class="fa fa-trash"></i>
                    <span class="text">delete</span>
                </a>
        </span>

    </div>

</div>
