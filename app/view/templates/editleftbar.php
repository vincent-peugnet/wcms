<div id="leftbar" class="bar">
    <input id="showleftpanel" name="workspace[showleftpanel]" value="1" class="toggle" type="checkbox" <?= $showleftpanel == true ? 'checked' : '' ?>>
    <label for="showleftpanel" class="toogle">◧</label>
    <div id="leftbarpanel" class="panel">

    <input type="hidden" name="thisdatemodif" value="<?= $page->datemodif('string') ?>">

    <details id="editinfo" open>
        <summary>Infos</summary>
        <fieldset>                        
            <label for="title">title :</label>
            <input type="text" name="title" id="title" value="<?= $page->title(); ?>">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" value="<?= $page->description(); ?>">
            <label for="tag">Tag(s) :</label>
            <input type="text" name="tag" id="tag" value="<?= $page->tag('string'); ?>">
            <label for="secure">Privacy level :</label>
            <select name="secure" id="secure">
                <option value="0" <?= $page->secure() == 0 ? 'selected' : '' ?>>public</option>
                <option value="1" <?= $page->secure() == 1 ? 'selected' : '' ?>>private</option>
                <option value="2" <?= $page->secure() == 2 ? 'selected' : '' ?>>not published</option>
            </select>
            <label for="date">Date</label>
            <input type="date" name="pdate" value="<?= $page->date('pdate') ?>" id="date">
            <label for="time">Time</label>
            <input type="time" name="ptime" value="<?= $page->date('ptime') ?>" id="time">

            <label for="favicon">Favicon</label>
            <select name="favicon" id="favicon">
            <?php
            if(!empty($page->templatecss()) && $page->template()['cssfavicon']) {
                ?>
                <option value="<?= $page->favicon() ?>">--using template favicon--</option>
                <?php
            } else {
                echo '<option value="">--no favicon--</option>';
            foreach ($faviconlist as $favicon) {
                ?>
                <option value="<?= $favicon ?>" <?= $page->favicon() === $favicon ? 'selected' : '' ?>><?= $favicon ?></option>
                <?php
                }
            }
            ?>
            </select>

            <div id="thumbnail">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept=".jpg, .JPG, .jpeg, .JPEG">
                <img src="<?= Model::thumbnailpath() ?><?= $page->id() ?>.jpg" alt="no-thumbnail">
            </div>




        </fieldset>
    </details>







    <details <?= !empty($page->templatebody()) || !empty($page->templatecss()) || !empty($page->templatejavascript()) ? 'open' : '' ?>>
        <summary>Template</summary>
            <fieldset>
            <label for="templatebody">BODY template</label>
            <select name="templatebody" id="templatebody">
            <option value="" <?= empty($page->templatebody()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($pagelist as $template) {
            ?>
                <option value="<?= $template ?>" <?= $page->templatebody() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            } 
            ?>
            </select>


            <label for="templatecss">CSS template</label>
            <select name="templatecss" id="templatecss">
            <option value="" <?= empty($page->templatecss()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($pagelist as $template) {
                ?>
                <option value="<?= $template ?>" <?= $page->templatecss() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            }
            ?>
            </select>
            
            <?php
            if(!empty($page->templatecss())) {
                ?>

                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oreccursivecss" value="reccursivecss" <?= in_array('reccursivecss', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="oreccursivecss">Reccursive template</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oquickcss" value="quickcss" <?= in_array('quickcss', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="oquickcss">Quickcss</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oexternalcss" value="externalcss" <?= in_array('externalcss', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="oexternalcss">External CSS</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="ofavicon" value="favicon" <?= in_array('favicon', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="ofavicon">Favicon</label>
                </div>
                <?php
            } else {
                foreach($page->templateoptions() as $option) {
                    if($option != 'externaljavascript') {
                        echo '<input type="hidden" name="templateoptions[]" value="'.$option.'">';
                    }
                }
            }
            
            ?>


            <label for="templatejavascript">Javascript template</label>
            <select name="templatejavascript" id="templatejavascript">
            <option value="" <?= empty($page->templatejavascript()) ? 'selected' : '' ?>>--no template--</option>
            <?php
            foreach ($pagelist as $template) {
                ?>
                <option value="<?= $template ?>" <?= $page->templatejavascript() === $template ? 'selected' : '' ?>><?= $template ?></option>
                <?php 
            }
            ?>
            </select>


            <?php
            if(!empty($page->templatejavascript())) {
            ?>
            <div class="subtemplate">
            <input type="checkbox" name="templateoptions[]" value="externaljavascript" id="oexternaljs" <?= in_array('externaljavascript', $page->templateoptions()) ? 'checked' : '' ?>>
            <label for="oexternaljs">external js</label>
            </div>

            <?php } else {
                if(in_array('externaljavascript', $page->templateoptions())) {
                    echo '<input type="hidden" name="templateoptions[]" value="externaljavascript">';
                }
                
            } ?>


            </fieldset>
    </details>
    <details id="advanced" <?= !empty($page->externalcss()) || !empty($page->customhead()) ? 'open' : '' ?>>
        <summary>Advanced</summary>
                




    <fieldset id="external">
        <label for="externalcss">External CSS</label>
        <input type="text" name="externalcss[]" id="externalcss" placeholder="add external adress">
        <?php
            foreach ($page->externalcss() as $css) {
                ?>
                <div class="checkexternal">
                <input type="checkbox" name="externalcss[]" id="<?= $css ?>" value="<?= $css ?>" checked>
                <label for="<?= $css ?>" title="<?= $css ?>"><?= $css ?></label>
                </div>
                <?php
            }
        ?>

        <label for="customhead">Custom head</label>
        <textarea name="customhead" wrap="off" spellcheck="false" rows="<?= $page->customhead('int') ?>"><?= $page->customhead() ?></textarea>

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