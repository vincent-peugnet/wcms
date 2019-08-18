<aside class="home">
    <details class="hidephone">
        <summary>Import page as file</summary>
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
    </details>



    <details class="hidephone">
        <summary>Columns</summary>
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
    </details>

    <details class="hidephone">
        <summary>Actions</summary>
        <form action="<?= $this->url('homerenderall') ?>" method="post">
            Render all pages
            </br>       
            <input type="submit" value="renderall">
        </form>
        </details>


</aside>