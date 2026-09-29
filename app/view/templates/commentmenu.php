<nav id="navbar" class="hbar">

    <div class="hbar-section">

        <?php if ($workspace->commentdisplay() === Wcms\Workspace::TIMELINE) : ?>
            <form
                action="<?= $this->url('commentmoderation', [], "?sortby=$sortby&order=$order&limit=$limit$filters") ?>"
                method="post"
                id="moderation"
            >
                <div class="dropdown-section">
                    <button type="submit">
                        <i class="fa fa-gavel"></i>
                        apply moderation
                    </button>
                </div>
            </form>
        <?php endif ?>

        <?php if ($workspace->commentdisplay() === Wcms\Workspace::LIST && $user->issupereditor()) : ?>
            <details name="menu" id="edit" class="dropdown">
                <summary>Edit</summary>
                <div class="dropdown-content">
                    <form action="<?= $this->url('commentmultiedit', [], "?sortby=$sortby&order=$order&limit=$limit$filters") ?>" method="post" id="multiedit">
                        <div class="dropdown-section">
                            <h3>edit</h3>
                            <h4>approval</h4>
                            <p class="field">
                                <label for="approved_keep">keep existing</label>
                                <input type="radio" name="approved" id="approved_keep" checked>
                            </p>
                            <p class="field">
                                <label for="approved_false">unapproved</label>
                                <input type="radio" name="approved" id="approved_false" value="0">
                            </p>
                            <p class="field">
                                <label for="approved_true">approved</label>
                                <input type="radio" name="approved" id="approved_true" value="1">
                            </p>
                            
                            <button type="submit">
                                edit
                            </button>
                        </div>
                    </form>
                </div>
            </details>
        <?php endif ?>

    </div>

    <div class="hbar-section">

        <div id="save-workspace">
            <form
                action="<?= $this->url('workspaceupdate') ?>"
                method="post"
                data-api="<?= $this->url('apiworkspaceupdate') ?>"
                id="workspace-form"
            >
                <input type="hidden" name="route" value="url">
                <input type="hidden" name="showcommentfilterpanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </div>

    </div>
    
</nav>
