<aside class="media">

    <details>
        <summary>File</summary>
            <div class="submenu">
                <h2>Upload file(s)</h2>
                <h3>
                    <label for="file"><i class="fa fa-upload"></i> Upload from computer</label>
                </h3>
                <p>max upload file size : <?= $maxuploadsize ?></p>
                <form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data">
                    <input type='file' id="file" name='file[]' multiple required>

                    <div>
                        <input type="hidden" name="idclean" value="0">
                        <input type="checkbox" name="idclean" id="idclean" value="1" checked>
                        <label for="idclean">clean filenames</label>
                    </div>

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
                <h2>New folder</h2>
                <form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post">
                    <label for="foldername"><i class="fa fa-folder"></i>  New folder</label>
                    <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>">
                <input type="submit" value="create folder">
                </form>


                <?php if($user->issupereditor()) { ?>
                <h2>Delete folder</h2>
                <form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post">
                    <input type="hidden" name="dir" value="<?= $mediaopt->dir() ?>/">
                    <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
                    <label for="confirmdeletefolder">Delete current folder and all it's content</label>
                    </br>
                    <input type="submit" value="delete folder" >
                </form>
                <?php } ?>

                <h2>Magic folders</h2>
                <h3><i class="fa fa-font"></i> fonts</h3>
                <a href="<?= $this->url('mediafontface', [], $mediaopt->getpathadress()) ?>">
                    <i class="fa fa-refresh"></i>regenerate @fontface CSS file
                </a>
            </div>
    </details>


    <details>
        <summary>Edit</summary>
        <div class="submenu">

            <?php if($user->issupereditor()) { ?>
            
            <h2>Move</h2>
            <form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit">
                <input type="hidden" name="route" value="<?= $mediaopt->getaddress() ?>">
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


    <details<?= $filtercode ? " open" : " " ?>>
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


            <p>Use this code to print the content of the current folder in a page</p>
            <input readonly class="code select-all" value="<?= $mediaopt->getcode() ?>" />
        </div>
    </details>



</aside>
