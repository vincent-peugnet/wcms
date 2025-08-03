<aside id="leftbar" class="toggle-panel-container">
    <input id="showeditorleftpanel" name="showeditorleftpanel" value="1" class="toggle-panel-toggle" type="checkbox" <?= $workspace->showeditorleftpanel() === true ? 'checked' : '' ?> form="workspace-form" >
    <label for="showeditorleftpanel" class="toggle-panel-label"><span><i class="fa fa-cog"></i></span></label>

    <div class="toggle-panel" id="leftbarpanel">
        <h1>Meta</h1>
        <div class="toggle-panel-content">

            <details id="editinfo" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>Informations</summary>
                <fieldset >                        
                    <p class="field">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" value="<?= $this->e($page->title()) ?>" form="update">
                    </p>
                    <p class="field">
                        <label for="description">Description</label>
                        <input type="text" name="description" id="description" value="<?= $this->e($page->description()) ?>" form="update">
                    </p>
                    <p class="field">
                        <label for="tag">Tag(s)</label>
                        <input type="text" name="tag" id="tag" value="<?= $page->tag('string'); ?>" form="update">
                    </p>
                    <p class="field">
                        <label for="secure">Privacy level</label>
                        <select name="secure" id="secure" form="update">
                            <option value="0" <?= $page->secure() == Wcms\Page::PUBLIC ? 'selected' : '' ?>>public</option>
                            <option value="1" <?= $page->secure() == Wcms\Page::PRIVATE ? 'selected' : '' ?>>private</option>
                            <option value="2" <?= $page->secure() == Wcms\Page::NOT_PUBLISHED ? 'selected' : '' ?>>not published</option>
                        </select>
                    </p>
                    <div class="flexrow">
                        <p class="field">
                            <label for="date">Date</label>
                            <input type="date" name="pdate" value="<?= $page->date('pdate') ?>" id="date" form="update">
                        </p>
                        <p class="field">
                            <label for="time">Time</label>
                            <input type="time" name="ptime" value="<?= $page->date('ptime') ?>" id="time" form="update">
                        </p>
                    </div>

                    <p class="field">
                        <label for="favicon">Favicon</label>
                        <select name="favicon" id="favicon" form="update">
                            <option value="">
                                <?= empty($page->templatebody()) ? '--default favicon--' : '--using template BODY favicon--' ?>
                            </option>
                            <?php foreach ($faviconlist as $favicon) : ?>
                                <option value="<?= $favicon ?>" <?= $page->favicon() === $favicon ? 'selected' : '' ?>>
                                    <?= $favicon ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </p>
                    
                    <p class="field">
                        <label for="thumbnail">Thumbnail</label>
                        <select name="thumbnail" id="thumbnail" form="update">
                            <option value="">
                                <?= empty($page->templatebody()) ? '--default thumbnail--' : '--using template BODY thumbnail--' ?>
                            </option>
                            <?php foreach ($thumbnaillist as $thumbnail) : ?>
                                <option value="<?= $thumbnail ?>" <?= $page->thumbnail() === $thumbnail ? 'selected' : '' ?>>
                                    <?= $thumbnail ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </p>

                    <?php if(!empty($page->thumbnail())) : ?>
                        <div id="showthumbnail">
                            <img src="<?= Wcms\Model::thumbnailpath() . $page->thumbnail() ?>">
                        </div>
                    <?php endif ?>
                
                </fieldset>
            </details>

            <?php if ($user->iseditor()) : ?>
                <details id="editinfo">
                    <summary>Authors</summary>
                    <fieldset>  
                        <?php foreach ($editorlist as $editor) : ?>
                            <p class="field">
                                <label for="<?= $editor->id() ?>" ><?= $editor->id() ?> <?= $editor->level() ?></label>    
                                <input
                                    type="checkbox"
                                    name="authors[]"
                                    id="<?= $editor->id() ?>"
                                    value="<?= $editor->id() ?>"
                                    form="update"
                                    <?= in_array($editor->id(), $page->authors()) ? 'checked' : '' ?>

                                    <?php /* safeguard against editor removing themself from authors too easily */ ?>
                                    <?= !$user->issupereditor() && $editor->id() === $user->id() ? 'disabled=""' : '' ?>
                                >
                            </p>
                        <?php endforeach ?>
                        <?php if(!$user->issupereditor()) : ?>
                            <input type="hidden" name="authors[]" value="<?= $user->id() ?>" form="update">
                        <?php endif ?>
                    </fieldset>
                </details>
            <?php endif ?>

            <details <?= $page->isgeo() && !$workspace->collapsemenu() ? 'open' : '' ?> id="geomap-details">
                <summary>Geolocalisation</summary>
                <div id="geomap"></div>
                <fieldset class="flexrow">
                    <p class="field">
                        <label for="latitude">latitude</label>
                        <input type="number" name="latitude" id="latitude" value="<?= is_null($page->latitude()) ? '' : $page->latitude() ?>" step="0.00001" min="<?= $page::LATITUDE_MIN ?>" max="<?= $page::LATITUDE_MAX ?>" form="update">
                    </p>
                    <p class="field">
                        <label for="longitude">longitude</label>
                        <input type="number" name="longitude" id="longitude" value="<?= is_null($page->longitude()) ? '' : $page->longitude() ?>" step="0.00001" min="<?= $page::LONGITUDE_MIN ?>" max="<?= $page::LONGITUDE_MAX ?>" form="update">
                    </p>
                </fieldset>
            </details>

            <details class="template" <?= !$workspace->collapsemenu() && ( !empty($page->templatebody()) || !empty($page->templatecss()) || !empty($page->templatejavascript()) ) ? 'open' : '' ?>>
                <summary>Template</summary>
                <fieldset >
                    <p class="field">
                        <label for="templatebody">
                            BODY template
                            <?php if(!empty($page->templatebody())) : ?>
                                <a href="<?= $this->upage('pageedit', $page->templatebody()) ?>" title="Edit template">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            <?php endif ?>
                        </label>                    
                        <select name="templatebody" id="templatebody" form="update">
                            <option value="" <?= empty($page->templatebody()) ? 'selected' : '' ?>>--no template--</option>
                            <?php foreach ($pagelist as $template) : ?>
                                <option value="<?= $template ?>" <?= $page->templatebody() === $template ? 'selected' : '' ?>><?= $template ?></option>
                            <?php endforeach ?>
                        </select>
                    </p>
                    <p class="field">
                        <label for="templatecss">
                            CSS template
                            <?php if(!empty($page->templatecss())) : ?>
                                <a href="<?= $this->upage('pageedit', $page->templatecss()) ?>" title="Edit template">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            <?php endif ?>
                        </label>
                        <select name="templatecss" id="templatecss" form="update">
                            <option value="%" <?= $page->templatecss() === null ? 'selected' : '' ?>>--same as body template--</option>
                            <option value="" <?= $page->templatecss() === '' ? 'selected' : '' ?>>--no template--</option>
                            <?php foreach ($pagelist as $template) : ?>
                                <option value="<?= $template ?>" <?= $page->templatecss() === $template ? 'selected' : '' ?>><?= $template ?></option>
                            <?php endforeach ?>
                        </select>
                    </p>
                    <p class="field">
                        <label for="templatejavascript">
                            Javascript template
                            <?php if(!empty($page->templatejavascript())) : ?>
                                <a href="<?= $this->upage('pageedit', $page->templatejavascript()) ?>" title="Edit template">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            <?php endif ?>
                        </label>                    
                        <select name="templatejavascript" id="templatejavascript" form="update">
                            <option value="%" <?= $page->templatejavascript() === null ? 'selected' : '' ?>>--same as body template--</option>
                            <option value="" <?= $page->templatejavascript() === '' ? 'selected' : '' ?>>--no template--</option>
                            <?php foreach ($pagelist as $template) : ?>
                                <option value="<?= $template ?>" <?= $page->templatejavascript() === $template ? 'selected' : '' ?>><?= $template ?></option>
                            <?php endforeach ?>
                        </select>
                    </p>

                </fieldset>
            </details>

            <details id="advanced" <?= !$workspace->collapsemenu() && ( !empty($page->externalcss()) || !empty($page->customhead()) || !empty($page->sleep()) || !empty($page->redirection()) ) ? 'open' : '' ?>>
                <summary>Advanced</summary>
                        
                <fieldset>
                    <p class="field">
                        <label for="externalcss">External CSS</label>
                        <input type="text" name="externalcss[]" id="externalcss" placeholder="add external address" form="update">
                    </p>
                    <?php foreach ($page->externalcss() as $css) : ?>
                        <p class="checkexternal field">
                            <label for="<?= hash('crc32', $css) ?>" title="<?= $this->e($css) ?>"><?= $this->e($css) ?></label>    
                            <input type="checkbox" name="externalcss[]" id="<?= hash('crc32', $css) ?>" value="<?= $this->e($css) ?>" form="update" checked>
                        </p>
                    <?php endforeach ?>
                    <p class="field">
                        <label for="customhead">Custom head</label>
                        <textarea name="customhead" wrap="off" spellcheck="false" rows="<?= $page->customhead('int') ?>" form="update"><?= $this->e($page->customhead()) ?></textarea>
                    </p>
                    <p class="field">
                        <label for="lang">Language</label>
                        <i>(default: <?= Wcms\Config::lang() ?> )</i>
                        <input type="text" name="lang" id="lang" value="<?= $page->lang() ?>" minlength="<?= Wcms\Config::LANG_MIN ?>" maxlength="<?= Wcms\Config::LANG_MAX ?>" form="update">
                    </p>
                    <p class="field">
                        <label for="sleep">Sleep time (s)</label>
                        <input type="number" name="sleep" id="sleep" value="<?= $page->sleep() ?>" min="0" max="180" form="update">
                    </p>
                    <p class="field">
                        <label for="redirection" title="page_id or URL like https://domain.org">Redirection</label>
                        <input type="text" name="redirection" id="redirection" value="<?= $this->e($page->redirection()) ?>" list="searchdatalist" form="update">
                    </p>
                    <p class="field">
                        <label for="refresh" title="Time before redirection (in seconds)">Refresh time</label>
                        <input type="number" name="refresh" value="<?= $page->refresh() ?>" id="refresh" min="0" max="180" form="update">
                    </p>
                    <p class="field">
                        <label for="password" title="specific page password protection">Password</label>
                        <input type="text" name="password" value="<?= $this->e($page->password()) ?>" id="password" min="0" max="64" form="update">
                    </p>
                </fieldset>
            </details>

            
        
        </div>
        
    </div>

</aside >
