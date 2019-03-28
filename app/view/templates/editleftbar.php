<div id="leftbar" class="bar">
    <input id="showleftpanel" name="workspace[showleftpanel]" value="1" class="toggle" type="checkbox" <?= $showleftpanel == true ? 'checked' : '' ?>>
    <label for="showleftpanel" class="toogle">◧</label>
    <div id="leftbarpanel" class="panel">

    <input type="hidden" name="thisdatemodif" value="<?= $art->datemodif('string') ?>">

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
                <option value="0" <?= $art->secure() == 0 ? 'selected' : '' ?>>public</option>
                <option value="1" <?= $art->secure() == 1 ? 'selected' : '' ?>>private</option>
                <option value="2" <?= $art->secure() == 2 ? 'selected' : '' ?>>not published</option>
            </select>
            <label for="date">Date</label>
            <input type="date" name="pdate" value="<?= $art->date('pdate') ?>" id="date">
            <label for="time">Time</label>
            <input type="time" name="ptime" value="<?= $art->date('ptime') ?>" id="time">

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

            <div id="thumbnail">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept=".jpg, .JPG, .jpeg, .JPEG">
                <img src="<?= Model::thumbnailpath() ?><?= $art->id() ?>.jpg" alt="no-thumbnail">
            </div>




        </fieldset>
    </details>







    <details <?= !empty($art->templatebody()) || !empty($art->templatecss()) || !empty($art->templatejavascript()) ? 'open' : '' ?>>
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
                <input type="checkbox" name="templateoptions[]" id="oreccursivecss" value="reccursivecss" <?= in_array('reccursivecss', $art->templateoptions()) ? 'checked' : '' ?>>
                <label for="oreccursivecss">Reccursive template</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oquickcss" value="quickcss" <?= in_array('quickcss', $art->templateoptions()) ? 'checked' : '' ?>>
                <label for="oquickcss">Quickcss</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oexternalcss" value="externalcss" <?= in_array('externalcss', $art->templateoptions()) ? 'checked' : '' ?>>
                <label for="oexternalcss">External CSS</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="ofavicon" value="favicon" <?= in_array('favicon', $art->templateoptions()) ? 'checked' : '' ?>>
                <label for="ofavicon">Favicon</label>
                </div>
                <?php
            } else {
                foreach($art->templateoptions() as $option) {
                    if($option != 'externaljavascript') {
                        echo '<input type="hidden" name="templateoptions[]" value="'.$option.'">';
                    }
                }
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


            <?php
            if(!empty($art->templatejavascript())) {
            ?>
            <div class="subtemplate">
            <input type="checkbox" name="templateoptions[]" value="externaljavascript" id="oexternaljs" <?= in_array('externaljavascript', $art->templateoptions()) ? 'checked' : '' ?>>
            <label for="oexternaljs">external js</label>
            </div>

            <?php } else {
                if(in_array('externaljavascript', $art->templateoptions())) {
                    echo '<input type="hidden" name="templateoptions[]" value="externaljavascript">';
                }
                
            } ?>


            </fieldset>
    </details>
    <details id="advanced" <?= !empty($art->externalcss()) || !empty($art->externalscript()) ? 'open' : '' ?>>
        <summary>Advanced</summary>
                




    <fieldset id="external">
        <label for="externalcss">External CSS</label>
        <input type="text" name="externalcss[]" id="externalcss" placeholder="add external adress">
        <?php
            foreach ($art->externalcss() as $css) {
                ?>
                <div class="checkexternal">
                <input type="checkbox" name="externalcss[]" id="<?= $css ?>" value="<?= $css ?>" checked>
                <label for="<?= $css ?>" title="<?= $css ?>"><?= $css ?></label>
                </div>
                <?php
            }
        ?>

        <label for="externalscript">External script</label>
        <input type="text" name="externalscript[]" id="externalscript" placeholder="add external adress">
        <?php
            foreach ($art->externalscript() as $script) {
                ?>
                <div class="checkexternal">
                <input type="checkbox" name="externalscript[]" id="<?= $script ?>" value="<?= $script ?>" checked>
                <label for="<?= $script ?>" title="<?= $script ?>"><?= $script ?></label>
                </div>
                <?php
            }
        ?>
    </fieldset>

    </details>
    <details id="help">
        <summary>Help</summary>
            <div>
                <?php $this->insert('edithelp') ?>
            </div>
                
    </details>

    </div>

</div>