<aside id="filter" class="toggle-panel-container">
    
    <input id="showeoptionspanel" name="showeoptionspanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form" <?= $workspace->showeoptionspanel() === true ? 'checked' : '' ?>>
    <label for="showeoptionspanel" class="toggle-panel-label"><span><i class="fa fa-filter"></i></span></label>
    
    <div class="toggle-panel" id="optionspanel">
        <h2>Filters</h2>

        <form action="<?= $this->url('home') ?>" method="get" class="toggle-panel-content flexcol">
            <p class="submit-field field">
                <?php if ($opt->isfiltered()) : ?>
                    <input type="submit" name="submit" value="reset">
                <?php endif ?>
                <input type="submit" name="submit" value="filter" class="filter">
            </p>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('limit') ? '1' : '0' ?>">
                <legend>Sort</legend>
                <p class="field">
                    <label for="sortby">Sort by</label>
                    <select name="sortby" id="sortby">
                        <?php foreach (Wcms\Opt::SORTBYLIST as $col) : ?>
                            <option value="<?= $col ?>" <?= ($opt->sortby() == $col ? "selected" : "") ?> >
                                <?= Wcms\Model::METADATAS_NAMES[$col] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </p>
                <p class="field">
                    <label for="asc">ascending</label>
                    <input type="radio" id="asc" name="order" value="1" <?= $opt->order() == '1' ? "checked" : "" ?> />
                </p>
                <p class="field">
                    <label for="desc">descending</label>
                    <input type="radio" id="desc" name="order" value="-1" <?= $opt->order() == '-1' ? "checked" : "" ?> />
                </p>
                <p class="field">
                    <label for="limit">limit</label>
                    <input type="number" name="limit" id="limit" value="<?= $opt->limit() ?>" min="0" max="9999">
                </p>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('secure') ? '1' : '0' ?>">
                <legend>Privacy</legend>                    
                <p class="field">
                    <label for="4">all</label>
                    <input type="radio" id="4" name="secure" value="4" <?= $opt->secure() == 4 ? "checked" : "" ?> />
                </p>
                <p class="field">
                    <label for="2">not published</label>
                    <input type="radio" id="2" name="secure" value="<?= Wcms\Page::NOT_PUBLISHED ?>" <?= $opt->secure() == Wcms\Page::NOT_PUBLISHED ? "checked" : "" ?> />
                </p>
                <p class="field">
                    <label for="1">private</label>
                    <input type="radio" id="1" name="secure" value="<?= Wcms\Page::PRIVATE ?>" <?= $opt->secure() == Wcms\Page::PRIVATE ? "checked" : "" ?> />
                </p>
                <p class="field">
                    <label for="0">public</label>
                    <input type="radio" id="0" name="secure" value="<?= Wcms\Page::PUBLIC ?>" <?= $opt->secure() == Wcms\Page::PUBLIC ? "checked" : "" ?> />
                </p>                        
            </fieldset>

            <fieldset class="flexcol tag" data-default="<?= $opt->isdefault('tagfilter') && $opt->tagcompare() != 'EMPTY' ? '1' : '0' ?>">
                <legend>
                    Tag 
                </legend>
                <input type="hidden" name="tagnot" value="0" <?= !$opt->tagnot() ? "checked" : '' ?>>
                
                <p class="field">
                    <label for="tagnot">NOT</label>
                    <input type="checkbox" name="tagnot" id="tagnot" value="1" <?= $opt->tagnot() ? "checked" : '' ?>>
                </p>
                <div class="flexrow">
                    <p class="field">
                        <label for="tag_OR">OR</label>    
                        <input type="radio" id="tag_OR" name="tagcompare" value="OR" <?= $opt->tagcompare() == "OR" ? "checked" : "" ?>>
                    </p>
                    <p class="field">
                        <label for="tag_AND">AND</label>
                        <input type="radio" id="tag_AND" name="tagcompare" value="AND" <?= $opt->tagcompare() == "AND" ? "checked" : "" ?>>
                    </p>
                    <p class="field">
                        <label for="tag_EMPTY">EMPTY</label>
                        <input type="radio" id="tag_EMPTY" name="tagcompare" value="EMPTY" <?= $opt->tagcompare() == "EMPTY" ? "checked" : "" ?>>
                    </p>
                </div>
                <?php foreach ($opt->taglist() as $tagfilter => $count) : ?>
                    <p class="field">
                        <label for="tag_<?= $tagfilter ?>"class="label-with-counter">
                            <span class="label"><?= $tagfilter ?></span>
                            <span class="counter tag_<?= $tagfilter ?>"><?= $count ?></span>
                        </label>    
                        <input
                            type="checkbox"
                            name="tagfilter[]"
                            id="tag_<?= $tagfilter ?>"
                            value="<?= $tagfilter ?>"
                            <?= in_array($tagfilter, $opt->tagfilter()) ? 'checked' : '' ?>
                        />
                    </p>
                <?php endforeach ?>
            </fieldset>

            <fieldset class="flexcol authors" data-default="<?= $opt->isdefault('authorfilter') && $opt->authorcompare() !== 'EMPTY' ? '1' : '0' ?>">
                <legend>Author(s)</legend>                            
                <div class="flexrow">
                    <p class="field">
                        <label for="author_OR">OR</label>    
                        <input type="radio" id="author_OR" name="authorcompare" value="OR" <?= $opt->authorcompare() == "OR" ? "checked" : "" ?>>
                    </p>
                    <p class="field">
                        <label for="author_AND">AND</label>
                        <input type="radio" id="author_AND" name="authorcompare" value="AND" <?= $opt->authorcompare() == "AND" ? "checked" : "" ?>>
                    </p>
                    <p class="field">
                        <label for="author_EMPTY">EMPTY</label>
                        <input type="radio" id="author_EMPTY" name="authorcompare" value="EMPTY" <?= $opt->authorcompare() == "EMPTY" ? "checked" : "" ?>>
                    </p>
                </div>
                <?php foreach ($opt->authorlist() as $authorfilter => $count) : ?>
                    <p class="field">
                        <label for="author_<?= $authorfilter ?>" class="label-with-counter">
                            <span class="label"><?= $authorfilter ?></span>
                            <span class="counter"><?= $count ?></span>
                        </label>
                        <input
                            type="checkbox"
                            name="authorfilter[]"
                            id="author_<?= $authorfilter ?>"
                            value="<?= $authorfilter ?>"
                            <?= in_array($authorfilter, $opt->authorfilter()) ? 'checked' : '' ?>
                        />
                    </p>
                <?php endforeach ?>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('since') && $opt->isdefault('until') ? '1' : '0' ?>">
                <legend>Date & time</legend>
                <p class="field">
                    <label for="since">since</label>
                    <input type="datetime-local" name="since" id="since" value="<?= !is_null($opt->since()) ? $opt->since('Y-m-d\TH:i') : "" ?>">
                </p>                    
                <p class="field">
                    <label for="until">until</label>
                    <input type="datetime-local" name="until" id="until" value="<?= !is_null($opt->until()) ? $opt->until('Y-m-d\TH:i') : "" ?>">
                </p>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('geo') ? '1' : '0' ?>">
                <legend>Geolocalisation</legend>
                <p class="field">
                    <input type="hidden" name="geo" value="0">
                    <label for="geo">page is geolocalized</label>
                    <input type="checkbox" name="geo" id="geo" value="1" <?= $opt->geo() ? 'checked' : '' ?>>
                </p>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('linkto') ? '1' : '0' ?>">
                <legend>Link to</legend>
                <p class="field">
                    <label for="linkto" title="filter pages that have links pointing to the following selected page">Link pointing to</label>
                    <select name="linkto" id="linkto">
                        <option value="" selected>-- not set --</option>
                        <?php
                        foreach ($opt->pageidlist() as $id) :
                            $selected = $id === $opt->linkto() ? ' selected': '';
                            ?>
                            <option value="<?= $id ?>" <?= $selected ?> ><?= $id ?></option>
                        <?php endforeach ?>
                    </select>
                </p>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('version') ? '1' : '0' ?>">
                <legend>Version</legend>
                <div class="flexrow">
                    <p class="field">
                        <label for="version0">all</label>
                        <input type="radio" id="version0" name="version" value="0" <?= $opt->version() === 0 ? "checked" : "" ?> />
                    </p>
                    <p class="field">
                        <label for="version1">v1</label>
                        <input type="radio" id="version1" name="version" value="<?= Wcms\Page::V1 ?>" <?= $opt->version() === Wcms\Page::V1  ? "checked" : "" ?> />
                    </p>
                    <p class="field">
                        <label for="version2">v2</label>
                        <input type="radio" id="version2" name="version" value="<?= Wcms\Page::V2 ?>" <?= $opt->version() === Wcms\Page::V2  ? "checked" : "" ?> />
                    </p>
                </div>
            </fieldset>

            <fieldset class="flexcol" data-default="<?= $opt->isdefault('invert') ? '1' : '0' ?>">
                <legend>Other</legend>                    
                <p class="field">
                    <input type="hidden" name="invert" value="0" <?= !$opt->invert() ? 'checked' : '' ?>>
                    <label for="invert">invert</label><br>
                    <input type="checkbox" name="invert" value="1" id="invert" <?= $opt->invert() ? 'checked' : '' ?>>
                </p>
            </fieldset>


            <p class="field submit-field">
                <input type="hidden" name="display" value="<?= $display ?>">
                <?php if ($opt->isfiltered()) : ?>
                    <input type="submit" name="submit" value="reset">
                <?php endif ?>
                <input type="submit" name="submit" value="filter" class="filter">
            </p>

        </form>
    </div>

</aside>
