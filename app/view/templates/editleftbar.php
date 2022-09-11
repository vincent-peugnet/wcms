<div id="leftbar" class="bar">
    <input
        id="showeditorleftpanel"
        name="workspace[showeditorleftpanel]"
        value="1"
        class="toggle"
        type="checkbox"
        form="workspace"
        <?= $showeditorleftpanel == true ? 'checked' : '' ?>
    >
    <label for="showeditorleftpanel" class="toogle">◧</label>
    <div id="leftbarpanel" class="panel">

    <input type="hidden" name="datemodif" value="<?= $page->datemodif('string') ?>" form="update">

    <details id="editinfo" open>
        <summary>Infos</summary>
        <fieldset>                        
            <label for="title">title :</label>
            <input type="text" name="title" id="title" value="<?= $page->title(); ?>" form="update">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" value="<?= $page->description(); ?>" form="update">
            <label for="tag">Tag(s) :</label>
            <input type="text" name="tag" id="tag" value="<?= $page->tag('string'); ?>" form="update">
            <label for="secure">Privacy level :</label>
            <select name="secure" id="secure" form="update">
                <option value="0" <?= $page->secure() == 0 ? 'selected' : '' ?>>public</option>
                <option value="1" <?= $page->secure() == 1 ? 'selected' : '' ?>>private</option>
                <option value="2" <?= $page->secure() == 2 ? 'selected' : '' ?>>not published</option>
            </select>
            <label for="date">Date</label>
            <input type="date" name="pdate" value="<?= $page->date('pdate') ?>" id="date" form="update">
            <label for="time">Time</label>
            <input type="time" name="ptime" value="<?= $page->date('ptime') ?>" id="time" form="update">

            <label for="favicon">Favicon</label>
            <select name="favicon" id="favicon" form="update">
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

            
            <label for="thumbnail">Thumbnail</label>
            <select name="thumbnail" id="thumbnail" form="update">
                <?php
                if(!empty($page->templatebody()) && $page->template()['cssthumbnail']) {
                    ?>
                    <option value="<?= $page->thumbnail() ?>">--using template thumbnail--</option>
                    <?php
                } else {
                    if(!file_exists(Wcms\Model::thumbnailpath() . $page->thumbnail())) {
                        echo '<option value="">--no thumbnail--</option>';
                    }
                foreach ($thumbnaillist as $thumbnail) {
                    ?>
                    <option value="<?= $thumbnail ?>" <?= $page->thumbnail() === $thumbnail ? 'selected' : '' ?>><?= $thumbnail ?></option>
                    <?php
                    }
                }
                ?>
            </select>

            <?php if(!empty($page->thumbnail())) { ?>
            <div id="showthumbnail">
                <img src="<?= Wcms\Model::thumbnailpath() . $page->thumbnail() ?>">
            </div>
            <?php } ?>

            




        </fieldset>
    </details>







    <details <?= !empty($page->templatebody()) || !empty($page->templatecss()) || !empty($page->templatejavascript()) ? 'open' : '' ?>>
        <summary>Template</summary>
            <fieldset>
            <label for="templatebody">BODY template</label>
            <select name="templatebody" id="templatebody" form="update">
                <option value="" <?= empty($page->templatebody()) ? 'selected' : '' ?>>--no template--</option>
                <?php
                foreach ($pagelist as $template) {
                ?>
                    <option value="<?= $template ?>" <?= $page->templatebody() === $template ? 'selected' : '' ?>><?= $template ?></option>
                    <?php 
                } 
                ?>
            </select>

            <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="othumbnail" value="thumbnail" form="update" <?= in_array('thumbnail', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="othumbnail">Thumbnail</label>
            </div>


            <label for="templatecss">CSS template</label>
            <select name="templatecss" id="templatecss" form="update">
                <option value="" <?= empty($page->templatecss()) ? 'selected' : '' ?>>--no template--</option>
                <?php
                foreach ($pagelist as $template) {
                    ?>
                    <option value="<?= $template ?>" <?= $page->templatecss() === $template ? 'selected' : '' ?>><?= $template ?></option>
                    <?php 
                }
                ?>
            </select>

            <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="orecursivecss" value="recursivecss" form="update" <?= in_array('recursivecss', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="orecursivecss">recursive template</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="oexternalcss" value="externalcss" form="update" <?= in_array('externalcss', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="oexternalcss">External CSS</label>
                </div>
                <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" id="ofavicon" value="favicon" form="update" <?= in_array('favicon', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="ofavicon">Favicon</label>
            </div>



            <label for="templatejavascript">Javascript template</label>
            <select name="templatejavascript" id="templatejavascript" form="update">
                <option value="" <?= empty($page->templatejavascript()) ? 'selected' : '' ?>>--no template--</option>
                <?php
                foreach ($pagelist as $template) {
                    ?>
                    <option value="<?= $template ?>" <?= $page->templatejavascript() === $template ? 'selected' : '' ?>><?= $template ?></option>
                    <?php 
                }
                ?>
            </select>



            <div class="subtemplate">
                <input type="checkbox" name="templateoptions[]" value="externaljavascript" id="oexternaljs" form="update" <?= in_array('externaljavascript', $page->templateoptions()) ? 'checked' : '' ?>>
                <label for="oexternaljs">external js</label>
            </div>



            </fieldset>
    </details>




    <details id="advanced" <?= !empty($page->externalcss()) || !empty($page->customhead()) || !empty($page->sleep()) || !empty($page->redirection()) ? 'open' : '' ?>>
        <summary>Advanced</summary>
                




    <fieldset id="external">
        <label for="externalcss">External CSS</label>
        <input type="text" name="externalcss[]" id="externalcss" placeholder="add external adress" form="update">
        <?php
            foreach ($page->externalcss() as $css) {
                ?>
                <div class="checkexternal">
                <input type="checkbox" name="externalcss[]" id="<?= $css ?>" value="<?= $css ?>" form="update" checked>
                <label for="<?= $css ?>" title="<?= $css ?>"><?= $css ?></label>
                </div>
                <?php
            }
        ?>

        <label for="customhead">Custom head</label>
        <textarea name="customhead" wrap="off" spellcheck="false" rows="<?= $page->customhead('int') ?>" form="update"><?= $page->customhead() ?></textarea>

        <label for="lang">Language</label>
        <i>(default: <?= Wcms\Config::lang() ?> )</i>
        <input type="text" name="lang" id="lang" value="<?= $page->lang() ?>" minlength="<?= Wcms\Config::LANG_MIN ?>" maxlength="<?= Wcms\Config::LANG_MAX ?>" form="update">

        <label for="sleep">Sleep time (s)</label>
        <input type="number" name="sleep" id="sleep" value="<?= $page->sleep() ?>" min="0" max="180" form="update">

        <label for="redirection" title="page_id or URL like https://domain.org">Redirection</label>
        <input type="text" name="redirection" id="redirection" value="<?= $page->redirection() ?>" list="searchdatalist" form="update">

        <label for="refresh" title="Time before redirection (in seconds)">Refresh time</label>
        <input type="number" name="refresh" value="<?= $page->refresh() ?>" id="refresh" min="0" max="180" form="update">

        <label for="password" title="specific page password protection">Password</label>
        <input type="text" name="password" value="<?= $page->password() ?>" id="password" min="0" max="64" form="update">

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
