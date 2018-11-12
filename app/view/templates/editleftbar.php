<div id="leftbar" class="bar">
    <input id="showleftpanel" name="workspace[showleftpanel]" value="1" class="toggle" type="checkbox" <?= $showleftpanel == true ? 'checked' : '' ?>>
    <label for="showleftpanel" class="toogle">◧</label>
    <div id="leftbarpanel" class="panel">
    <details id="editinfo" open>
        <summary>Infos</summary>
        <fieldset>                        
            <label for="title">title :</label>
            <input type="text" name="title" id="title" value="<?= $art->title(); ?>">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" value="<?= $art->description(); ?>">
            <label for="tag">Tag(s) :</label>
            <input type="text" name="tag" id="tag" value="<?= $art->tag('string'); ?>">
            <label for="secure">Privacy level :</label>
            <select name="secure" id="secure">
                <option value="0" <?= $art->secure() == 0 ? 'selected' : '' ?>>0</option>
                <option value="1" <?= $art->secure() == 1 ? 'selected' : '' ?>>1</option>
                <option value="2" <?= $art->secure() == 2 ? 'selected' : '' ?>>2</option>
                <option value="3" <?= $art->secure() == 3 ? 'selected' : '' ?>>3</option>
            </select>
        </fieldset>
    </details>
    <details>
        <summary>Advanced</summary>
            <fieldset>
            <h3>Template options</h3>
            <ul>
            <?php
            foreach ($tablist as $element => $value) {
                if(isset($art->template()[$element])) {
                    $template = $art->template()[$element];
                } else {
                    $template = '';
                }
                echo '<li>';
                echo '<label for="'.$element.'template">'.$element.'</label>';
                echo '<select name="template['.$element.']" id="'.$element.'template">';
                ?>
                <option value="" <?= $template === '' ? 'selected' : '' ?>>--no template--</option>
                <?php
                foreach ($artlist as $artid ) {

                    ?>
                    <option value="<?= $artid ?>" <?= $template === $artid ? 'selected' : '' ?>><?= $artid ?></option>
                    <?php
                }
                echo '</select>';
                echo '</li>';
            }


            ?>
            </ul>
            </fieldset>
    </details>
    <details id="editcss" open>
        <summary>Quick CSS</summary>
        
    </details>
    <details>
        <summary>Help</summary>
        <div id="help">
            <?php $this->insert('edithelp') ?>

        </div>
    </details>

    </div>

</div>