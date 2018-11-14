<div id="rightbar" class="bar">
    <input id="showrightpanel" name="workspace[showrightpanel]" value="1" class="toggle" type="checkbox"  <?= $showrightpanel == true ? 'checked' : '' ?>>
    <label for="showrightpanel" class="toogle">◧</label>
    <div id="rightbarpanel" class="panel">
    <details id="linkassist" open>
        <summary>Links</summary>
        <?php
        foreach ($artlist as $item ) {
            ?>
            <a href="<?= $this->uart('artedit', $item) ?>" target="_blank"><?= $item ?></a>
            <?php
        }

        ?>
    </details>
    <details id="fonts" open>
        <summary>Fonts</summary>
        <select multiple>
        <?php
        foreach ($fonts as $font ) {
            echo '<option value="'.$font.'">'.$font.'</option>';
        }
        ?>
        </select>
    </details>
   
    </div>

</div>