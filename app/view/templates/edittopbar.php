<nav id="edittopbar" class="hbar">

    <div class="hbar-section" id="pagemenu">
        
        <form action="<?= $this->upage('pageupdate', $page->id()) ?>" method="post" id="update" data-api="<?= $this->upage('apipageupdate', $page->id()) ?>" >
            <button type="submit" accesskey="s" >
                <i class="fa fa-save"></i>
                <span class="text">update</span>
                <span id="headid">
                    <span class="pageid"><?= $page->id() ?></span> 
                    <span id="editstatus"></span>
                </span>
            </button>
        </form>

        <a href="<?= $this->upage('pageread', $page->id()) ?>" target="<?= $target ?>" id="display">
            <i class="fa fa-eye"></i> <span class="text">display</span>
        </a>

        <a href="<?= $this->upage('pagedownload', $page->id()) ?>"  id="download">
            <i class="fa fa-download"></i> <span class="text">download</span>
        </a>        

        <?php if($this->candeletepage($page)) : ?>
            <a href="<?= $this->upage('pagedelete', $page->id()) ?>" id="delete">
                <i class="fa fa-trash"></i> <span class="text">delete</span>
            </a>
        <?php endif ?>

    </div>

    <div class="hbar-section" id="workspacemenu">

        <span class="fontsize">
            <label for="editfontsize">
                <i class="fa fa-text-height"></i>
            </label>
            <input type="number" name="fontsize" value="<?= $workspace->fontsize() ?>" id="editfontsize" min="<?= Wcms\Workspace::FONTSIZE_MIN ?>" max="<?= Wcms\Workspace::FONTSIZE_MAX ?>" form="workspace-form">
        </span>
                
        <span class="highlighttheme">
            <label for="edithighlighttheme">
                <i class="fa fa-adjust"></i>
            </label>
            <select name="highlighttheme" form="workspace-form" id="edithighlighttheme">
                <?= options(Wcms\Workspace::THEMES, $workspace->highlighttheme(), true) ?>
            </select>
        </span>

        <div id="save-workspace">
            <form action="<?= $this->url('workspaceupdate') ?>" method="post" id="workspace-form" data-api="<?= $this->url('apiworkspaceupdate') ?>" >
                <input type="hidden" name="page" value="<?= $page->id() ?>">
                <input type="hidden" name="showeditorleftpanel" value="0">
                <input type="hidden" name="showeditorrightpanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </span>

    </div>

</nav>
