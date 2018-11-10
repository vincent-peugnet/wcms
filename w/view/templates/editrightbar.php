<div id="rightbar">
    <input id="showrightpanel" name="workspace[showrightpanel]" value="1" class="toggle" type="checkbox"  <?= $showrightpanel == true ? 'checked' : '' ?>>
    <label for="showrightpanel" class="toogle">◧</label>
    <div id="rightbarpanel" class="panel">
    <details id="linkassist" open>
        <summary>Links</summary>
        <?php
        foreach ($artlist as $item ) {
            ?>
            <a href="?id=<?= $item ?>&aff=edit"><?= $item ?></a>
            <input type="text" value="[<?= $item ?>](=<?= $item ?>)">
            <?php
        }

        ?>
    </details>
   
    </div>

</div>