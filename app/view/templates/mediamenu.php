<aside class="media">

    <details>
        <summary>File</summary>
            <div class="submenu">
                <h2>Upload file(s)</h2>
                <h3>
                    <label for="file"><i class="fa fa-upload"></i> Upload from computer</label>
                </h3>
                <form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data">
                    <input type='file' id="file" name='file[]' multiple required>

                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                    <input type="submit" value="upload">
                </form>
                <h3>
                    <label for="url"><i class="fa fa-cloud-upload"></i> Upload from URL</label>
                </h3>
                <form id="addurlmedia" action="<?= $this->url('mediaurlupload') ?>" method="post">
                    <input type="text" name="url" id="url" placeholder="paste url here">
                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                    <input type="submit" value="upload">
                </form>
                <h2>Folder</h2>
                <form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post">
                    <label for="foldername">📂 New folder</label>
                    <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                <input type="submit" value="create folder">
                </form>
            </div>
    </details>


    <details>
        <summary>Edit</summary>
        <div class="submenu">

            <?php if($user->issupereditor()) { ?>

            <h2>Folder</h2>
            <form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post">
                <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>/">
                <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
                <label for="confirmdeletefolder">Delete actual folder and all it's content</label>
                </br>
                <input type="submit" value="delete folder" >
            </form>

            
            <h2>Move</h2>
            <form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit">
                <input type="hidden" name="route" value="<?= $mediaopt->getadress() ?>">
                <input type="hidden" name="path" value="<?= $mediaopt->dir() ?>">
                <label for="moveto">Move selected medias to a new directory</label>
                </br>
                <select name="dir" id="moveto" >
                    <option selected>---select destination---</option>
                    <option value="<?= Wcms\Model::MEDIA_DIR ?>">/</option>
                    <?php
                        foreach ($pathlist as $path) {
                            echo '<option value="' . Wcms\Model::MEDIA_DIR . $path . '">' . $path . '</option>';
                        }
                        ?>
                </select>
                <input type="submit" name="action" value="move" >
                <h2>Delete</h2>
                Delete selected medias
                </br>
                <input type="submit" name="action" value="delete" >
            </form>



            <?php } ?>
        </div>
    </details>


    <details>
        <summary>Filter</summary>
        <div class="submenu">
            <h2>Print folder content</h2>
            <form action="" method="post">
                <input type="hidden" name="query" value="1">
                <input type="hidden" name="filename" value="0">
                <input type="checkbox" name="filename" id="filename" value="1" <?= $mediaopt->filename() ? "checked" : "" ?> >
                <label for="filename">add filenames under images, sounds and videos</label>
                <br>
                <input type="submit" value="generate">
            </form>


            <p>Use this code to print the content of the actual folder in a page</p>
            <input readonly class="code select-all" value="<?= $mediaopt->getcode() ?>" />
        </div>
    </details>







    <details id="bookmarks">
        <summary>Bookmarks</summary>
        <div class="submenu">
            <h2>Personnal</h2>
            <?php if(!empty($user->bookmark())) { ?>
            <form action="<?= $this->url('userbookmark') ?>" method="post">
            <ul>
            <?php foreach ($user->bookmark() as $bookmark) { ?>
                <?php if($bookmark->route() === 'media') { ?>
                <li>
                    <input type="checkbox" name="id[]" value="<?= $bookmark->id() ?>" id="bookmark_<?= $bookmark->id() ?>">
                    <label for="bookmark_<?= $bookmark->id() ?>" title="<?= $bookmark->query() ?>"><?= $bookmark->id() ?></label>
                </li>
                <?php } ?>
            <?php } ?>
            </ul>
            <input type="hidden" name="action" value="del">
            <input type="hidden" name="route" value="media">
            <input type="hidden" name="user" value="<?= $user->id() ?>">
            <input type="submit" value="delete selected">
            </form>
            <?php } else { ?>
                <p>This will store your filters settings as a Bookmark that only you can use.</p>
            <?php } ?>
            <form action="<?= $this->url('userbookmark') ?>" method="post">
                <select name="icon" id="icon">
                    <?= options(Wcms\Model::BOOKMARK_ICONS, null, true) ?>
                </select>
                <input type="text" name="id" placeholder="bookmark id" minlength="1" maxlength="16" required>
                <input type="hidden" name="query" value="<?= $mediaopt->getadress() ?>">
                <input type="hidden" name="route" value="media">
                <input type="hidden" name="user" value="<?= $user->id() ?>">
                <input type="submit" name="action" value="add">
            </form>
        </div>
    </details>




</aside>