<nav id="navbar" class="hbar">

    <div class="hbar-section">

        <details name="menu" id="json" class="dropdown">
            <summary>File</summary>
            <div class="dropdown-content">
                <div class="dropdown-section">
                    <h3>Cache</h3>
                    <?php if ($user->isadmin()) : ?>
                        <i>Delete the cache</i>
                        <p class="field submit-field">
                            <a href="<?= $this->url('flushurlcache') ?>" class="button">
                                <i class="fa fa-trash"></i> Flush URL cache
                            </a>
                        </p>
                    <?php endif ?>
                    <i>Remove unused URLs</i>
                    <p class="field submit-field">
                        <a href="<?= $this->url('cleanurlcache') ?>" class="button">
                            <i class="fa fa-recycle"></i> Clean URL cache
                        </a>
                    </p>
                </div>
            </div>
        </details>


        <details name="menu" id="edit" class="dropdown">
            <summary>Edit</summary>
            <div class="dropdown-content">
                <div class="dropdown-section">
                    <h3>Checking</h3>
                    <i>Re-check selected URLs</i>
                    <form action="<?= $this->url('urledit') ?>" method="post" id="urledit">
                        <p class="field submit-field">
                            <button type="submit" name="action" value="check">
                                <i class="fa fa-refresh"></i>
                                <span>re-check</span>
                            </button>
                        </p>
                    </form>
                </div>
            </div>
        </details>

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
                <input type="hidden" name="showurlfilterpanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </div>

    </div>
    
</nav>
