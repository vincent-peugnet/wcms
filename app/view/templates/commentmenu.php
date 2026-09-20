<nav id="navbar" class="hbar">

    <div class="hbar-section">

        <details name="menu" id="json" class="dropdown">
            <summary>File</summary>
            <div class="dropdown-content">
                <div class="dropdown-section">
                </div>
            </div>
        </details>


        <details name="menu" id="edit" class="dropdown">
            <summary>Edit</summary>
            <div class="dropdown-content">
                <div class="dropdown-section">
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
                <input type="hidden" name="showcommentfilterpanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </div>

    </div>
    
</nav>
