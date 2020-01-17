<aside class="media">

    <details>
        <summary>File</summary>
            <div class="submenu">
                <h2>Upload File(s)</h2>
                <form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data">
                    <label for="file">🚀 Upload file(s)</label>
                    <input type='file' id="file" name='file[]' multiple required>
                    <input type="hidden" name="dir" value="<?= $dir ?>">
                    <input type="submit" value="upload">
                </form>
                <h2>Folder</h2>
                <form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post">
                    <label for="foldername">📂 New folder</label>
                    <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
                    <input type="hidden" name="dir" value="<?= $dir ?>">
                <input type="submit" value="create folder">
                </form>
            </div>
    </details>


    <details>
        <summary>Edit</summary>
        <div class="submenu">

            <?php if($user->issupereditor()) { ?>

            <h2>Folder</h2>
            <form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post" class="hidephone">
                <input type="hidden" name="dir" value="<?= $dir ?>/">
                <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
                <label for="confirmdeletefolder">Delete actual folder and all it's content</label>
                </br>
                <input type="submit" value="delete folder" >
            </form>

            
            <h2>Move</h2>
            <form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit">
                <input type="hidden" name="path" value="<?= $dir ?>">
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
            


            <p>Use this code to print the content of the actual folder in a page</p>
            <code>%MEDIA?path=<?= substr($dir, 6) ?>&sortby=<?= $opt['sortby'] ?>&order=<?= $opt['order'] ?>%</code>
        </div>
    </details>

</aside>