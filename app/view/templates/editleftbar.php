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
            <label for="date">Date</label>
            <input type="date" name="pdate" value="<?= $art->date('pdate') ?>" id="date">
            <label for="time">Time</label>
            <input type="time" name="ptime" value="<?= $art->date('ptime') ?>" id="time">
        </fieldset>
    </details>
    <details open>
        <summary>Tempalte</summary>
            <fieldset>
            <label for="templatebody">BODY template</label>
            <select name="templatebody" id="templatebody">
            <option value="" <?= empty($art->templatebody()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($artlist as $template) {
            ?>
                <option value="<?= $template ?>" <?= $art->templatebody() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            }
            ?>
            </select>


            <label for="templatecss">CSS template</label>
            <select name="templatecss" id="templatecss">
            <option value="" <?= empty($art->templatecss()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($artlist as $template) {
                ?>
                <option value="<?= $template ?>" <?= $art->templatecss() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            }
            ?>
            </select>
            
            <?php
            if(!empty($art->templatecss())) {
                ?>
                <div class="subtemplate">
                <input type="checkbox" name="ireccursivecss" id="ireccursivecss" <?= $art->template()['cssreccursive'] === true ? 'checked' : '' ?>>
                <label for="ireccursivecss">Reccursive template</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="iquickcss" id="iquickcss" <?= $art->template()['cssquickcss'] === true ? 'checked' : '' ?>>
                <label for="iquickcss">Quickcss</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="iexternalcss" id="iexternalcss" <?= $art->template()['externalcss'] === true ? 'checked' : '' ?>>
                <label for="iexternalcss">External CSS</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="ifavicon" id="ifavicon" <?= $art->template()['cssfavicon'] === true ? 'checked' : '' ?>>
                <label for="ifavicon">Favicon</label>
                </div>
                <?php
            }

            ?>


            <label for="templatejavascript">Javascript template</label>
            <select name="templatejavascript" id="templatejavascript">
            <option value="" <?= empty($art->templatejavascript()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($artlist as $template) {
                ?>
                <option value="<?= $template ?>" <?= $art->templatejavascript() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            }
            ?>
            </select>
            <div class="subtemplate">
            <input type="checkbox" name="iexternaljs" id="iexternaljs">
            <label for="iexternaljs">external js</label>
            </div>


            </fieldset>
    </details>
    <details id="advanced" open>
        <summary>Advanced</summary>
                
        <fieldset>

        <?php

        ?>

        <label for="favicon">Favicon</label>
        <select name="favicon" id="favicon">
        <?php
        if(!empty($art->templatecss()) && $art->template()['cssfavicon']) {
            ?>
            <option value="<?= $art->favicon() ?>">--using template favicon--</option>
            <?php
        } else {
            echo '<option value="">--no favicon--</option>';
        foreach ($faviconlist as $favicon) {
            ?>
            <option value="<?= $favicon ?>" <?= $art->favicon() === $favicon ? 'selected' : '' ?>><?= $favicon ?></option>
            <?php
            }
        }
        ?>
        </select>
        </fieldset>

    </details>
    <details>
        <summary>Help</summary>
        <div id="help">
            <?php $this->insert('edithelp') ?>

        </div>
    </details>

    </div>

</div>