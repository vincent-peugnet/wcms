<aside id="edittopbar">

    <span class="menu" id="pagemenu">
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
            <a href="<?= $this->upage('pageread', $page->id()) ?>" target="<?= $target ?>" id="display">
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


        <?php if($this->candeletepage($page)) { ?>
            <span id="delete">
                    <a href="<?= $this->upage('pagedelete', $page->id()) ?>">
                        <i class="fa fa-trash"></i>
                        <span class="text">delete</span>
                    </a>
            </span>
        <?php } ?>

    </span>
    <span class="menu" id="workspacemenu">

        <span id="fontsize">
            <label for="fontsize">
                <i class="fa fa-text-height"></i>
            </label>
            <input type="number" name="fontsize" value="<?= $workspace->fontsize() ?>" id="editfontsize" min="<?= Wcms\Workspace::FONTSIZE_MIN ?>" max="<?= Wcms\Workspace::FONTSIZE_MAX ?>" form="workspace-form">
        </span>

        <span id="highlighttheme">
            <label for="fontsize">
                <i class="fa fa-adjust"></i>
            </label>
            <select name="highlighttheme" form="workspace-form" id="edithighlighttheme">
                <?= options(Wcms\Workspace::THEMES, $workspace->highlighttheme(), true) ?>
            </select>
        </span>

        <span id="save-workspace">

            <form
                action="<?= $this->url('workspaceupdate') ?>"
                method="post"
                id="workspace-form"
                data-api="<?= $this->url('apiworkspaceupdate') ?>"
            >
                <input type="hidden" name="page" value="<?= $page->id() ?>">
                <input type="hidden" name="showeditorleftpanel" value="0">
                <input type="hidden" name="showeditorrightpanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </span>

</span>

</aside>
