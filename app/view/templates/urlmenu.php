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

    </div>
    
</nav>
