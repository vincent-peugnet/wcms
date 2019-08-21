<aside class="home">
    <details class="hidephone" id="json">
        <summary>File</summary>
            <div class="submenu">
                <h2>Import page as file</h2>
            <form action="<?= $this->url('artupload') ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="pagefile" id="pagefile" accept=".json">
            <label for="pagefile">JSON Page file</label>
            <input type="hidden" name="erase" value="0">
            <input type="hidden" name="datecreation" value="0">
            </br>
            <input type="text" name="id" id="id" placeholder="new id (optionnal)">
            <label for="id">change ID</label>
            </br>
            <input type="checkbox" name="datecreation" id="datecreation" value="1">
            <label for="datecreation">Reset date creation as now</label>
            </br>
            <input type="checkbox" name="author" id="author" value="1">
            <label for="author">Reset author(s) as just you</label>
            </br>
            <input type="checkbox" name="erase" id="erase" value="1">
            <label for="erase">Replace if already existing</label>
            </br>
            <input type="submit" value="upload">
            </form>
            </div>
    </details>



    <details class="hidephone" id="columns">
        <summary>Columns</summary>
        <div class="submenu">
            <h2>Columns</h2>
        <form action="<?= $this->url('homecolumns') ?>" method="post">
        <ul>
        <?php
        foreach (Model::COLUMNS as $col) { ?>
            <li>
            <input type="checkbox" name="columns[]" value="<?= $col ?>" id="col_<?= $col ?>" <?= in_array($col, $user->columns()) ? 'checked' : '' ?>>
            <label for="col_<?= $col ?>"><?= $col ?></label>
            </li>
            <?php } ?>
        </ul>
        <input type="submit" value="update columns">
        </form>
        </div>
    </details>

    <details class="hidephone" id="actions">
        <summary>Actions</summary>
        <div class="submenu">
            <h2>Rendering</h2>
        <form action="<?= $this->url('homerenderall') ?>" method="post">
            Render all pages
            </br>       
            <input type="submit" value="renderall">
        </form>
        </div>
        </details>

    <details class="hidephone" id="bookmarks">
        <summary>Bookmarks</summary>
        <div class="submenu">
            <h2>Public</h2>
            <?php if(!empty(Config::bookmark())) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
            <ul>
            <?php foreach (Config::bookmark() as $id => $query) { ?>  
                <li>
                <label for="public-bookmark_<?= $id ?>">
                    <a href="<?= $query ?>" title="<?= $query ?>"><?= $id ?></a>
                    </label>
                    <?php if($user->issupereditor()) { ?>
                        <input type="checkbox" name="id[]" value="<?= $id ?>" id="public-bookmark_<?= $id ?>">
                    <?php } ?>
                </li>
            <?php } ?>
            </ul>
            <input type="hidden" name="action" value="del">
            <input type="submit" value="delete selected" class="floatright">
            </form>
            <?php } elseif($user->issupereditor()) { ?>
                <p>This will store your filters settings as a Bookmark that every editors users can use.</p>
            <?php } else { ?>
                <p>No public Bookmarks yet</p>
            <?php } ?>
            <?php if($user->issupereditor()) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
                <input type="text" name="id" placeholder="bookmark id">
                <input type="hidden" name="query" value="<?= $opt->getadress() ?>">
                <input type="submit" name="action" value="add">
            </form>
            <?php } ?>
            <h2>Personnal</h2>
            <?php if(!empty($user->bookmark())) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
            <ul>
            <?php foreach ($user->bookmark() as $id => $query) { ?>  
                <li>
                    <a href="<?= $query ?>" title="<?= $query ?>"><?= $id ?></a>
                    <input type="checkbox" name="id[]" value="<?= $id ?>">
                </li>
            <?php } ?>
            </ul>
            <input type="hidden" name="action" value="del">
            <input type="hidden" name="user" value="<?= $user->id() ?>">
            <input type="submit" value="delete selected" class="floatright">
            </form>
            <?php } else { ?>
                <p>This will store your filters settings as a Bookmark that only you can use.</p>
            <?php } ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
                <input type="text" name="id" placeholder="bookmark id">
                <input type="hidden" name="query" value="<?= $opt->getadress() ?>">
                <input type="hidden" name="user" value="<?= $user->id() ?>">
                <input type="submit" name="action" value="add">
            </form>
        </div>
    </details>


</aside>