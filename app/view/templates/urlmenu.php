<nav id="navbar" class="hbar">

    <div class="hbar-section">

        <details name="menu" id="json" class="dropdown">
            <summary>File</summary>
            <div class="dropdown-content">
                <div class="dropdown-section">
                    <h3>Cache</h3>
                    <?php if ($user->isadmin()) : ?>
                        <p class="field submit-field">
                            <a href="<?= $this->url('flushurlcache') ?>" class="button">
                                <i class="fa fa-trash"></i> Flush URL cache
                            </a>
                        </p>
                    <?php endif ?>
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
                    <form action="<?= $this->url('urledit') ?>" method="post" id="urledit">
                        <i>Edit selected URLs</i>
                        <button type="submit" name="action" value="check">
                            <i class="fa fa-refresh"></i>
                            <span>re-check</span>
                        </button>
                    </form>
                </div>
            </div>
        </details>

    </div>
    
</nav>
