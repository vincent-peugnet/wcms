<aside id="edittopbar">

    <div id="editmenu">
        <span>
            <form
                action="<?= $this->upage('pageupdate', $page->id()) ?>"
                method="post"
                id="update"
                data-api="<?= $this->upage('apipageupdate', $page->id()) ?>"
            >
                <button type="submit" accesskey="s" >
                    <i class="fa fa-save"></i>
                    <span class="text">update</span>
                </button>
            </form>
        </span>

        <span id="headid">
            <?= $page->id() ?>
        </span>

        <span>
            <a href="<?= $this->upage('pageread/', $page->id()) ?>" target="<?= $page->id() ?>" id="display">
                <i class="fa fa-eye"></i>
                <span class="text">display</span>
            </a>
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

        <span id="fontsize">
            <label for="fontsize">
                <i class="fa fa-text-height"></i>
            </label>
            <input type="number" name="fontsize" value="<?= Wcms\Config::fontsize() ?>" id="editfontsize" min="5" max="99" form="workspace-form">
        </span>

        <span id="save-workspace">

            <form action="" method="post" id="workspace-form">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </span>

    </div>

</aside>
