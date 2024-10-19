<div class="bar" id="filter">

    
    <input id="showeoptionspanel" name="showeoptionspanel" value="1" class="toggle" type="checkbox" form="workspace-form" <?= $workspace->showeoptionspanel() === true ? 'checked' : '' ?>>
    <label for="showeoptionspanel" class="toogle">◧</label>
    
    <div class="panel" id="optionspanel">
        <h2>Filters</h2>


        <form action="<?= $this->url('home') ?>" method="get">
            <input type="submit" name="submit" value="filter" class="filter">
            <?php if ($opt->isfiltered()) : ?>
                <input type="submit" name="submit" value="reset">
            <?php endif ?>

            <div id="optfield">



                <fieldset data-default="<?= $opt->isdefault('limit') ? '1' : '0' ?>">
                    <legend>Sort</legend>
                    <div>
                        <select name="sortby" id="sortby">
                            <?php foreach (Wcms\Opt::SORTBYLIST as $col) :
                                $name = Wcms\Model::METADATAS_NAMES[$col]; ?>
                                <option value="<?= $col ?>" <?= ($opt->sortby() == $col ? "selected" : "") ?> ><?= $name ?></option>
                            <?php endforeach ?>
                        </select>
                        <div>
                            <input type="radio" id="asc" name="order" value="1" <?= $opt->order() == '1' ? "checked" : "" ?> />
                            <label for="asc">ascending</label>
                        </div>
                        <div>
                            <input type="radio" id="desc" name="order" value="-1" <?= $opt->order() == '-1' ? "checked" : "" ?> />
                            <label for="desc">descending</label>
                        </div>
                        <div>
                            <input type="number" name="limit" id="limit" value="<?= $opt->limit() ?>" min="0" max="9999">
                            <label for="limit">limit</label>
                        </div>
                    </div>
                </fieldset>




                <fieldset data-default="<?= $opt->isdefault('secure') ? '1' : '0' ?>">
                    <legend>Privacy</legend>
                    <div>
                        <ul>
                            <li><input type="radio" id="4" name="secure" value="4" <?= $opt->secure() == 4 ? "checked" : "" ?> /><label for="4">all</label></li>
                            <li><input type="radio" id="2" name="secure" value="<?= Wcms\Page::NOT_PUBLISHED ?>" <?= $opt->secure() == Wcms\Page::NOT_PUBLISHED ? "checked" : "" ?> /><label for="2">not published</label></li>
                            <li><input type="radio" id="1" name="secure" value="<?= Wcms\Page::PRIVATE ?>" <?= $opt->secure() == Wcms\Page::PRIVATE ? "checked" : "" ?> /><label for="1">private</label></li>
                            <li><input type="radio" id="0" name="secure" value="<?= Wcms\Page::PUBLIC ?>" <?= $opt->secure() == Wcms\Page::PUBLIC ? "checked" : "" ?> /><label for="0">public</label></li>
                        </ul>
                    </div>
                </fieldset>




                <fieldset class="tag" data-default="<?= $opt->isdefault('tagfilter') && $opt->tagcompare() != 'EMPTY' ? '1' : '0' ?>">
                    <legend>Tag</legend>
                    <div>
                        <input type="hidden" name="tagnot" value="0" <?= !$opt->tagnot() ? "checked" : '' ?>>
                        <input type="checkbox" name="tagnot" id="tagnot" value="1" <?= $opt->tagnot() ? "checked" : '' ?>>
                        <label for="tagnot">NOT</label>
                        <br>

                        
                        <input type="radio" id="tag_OR" name="tagcompare" value="OR" <?= $opt->tagcompare() == "OR" ? "checked" : "" ?>>
                        <label for="tag_OR">OR</label>
                        <input type="radio" id="tag_AND" name="tagcompare" value="AND" <?= $opt->tagcompare() == "AND" ? "checked" : "" ?>>
                        <label for="tag_AND">AND</label>
                        <input type="radio" id="tag_EMPTY" name="tagcompare" value="EMPTY" <?= $opt->tagcompare() == "EMPTY" ? "checked" : "" ?>>
                        <label for="tag_EMPTY">EMPTY</label>
                        
                        <hr>

                        <ul>
                            <?php foreach ($opt->taglist() as $tagfilter => $count) : ?>
                                <li>
                                    <input
                                        type="checkbox"
                                        name="tagfilter[]"
                                        id="tag_<?= $tagfilter ?>"
                                        value="<?= $tagfilter ?>"
                                        <?= in_array($tagfilter, $opt->tagfilter()) ? 'checked' : '' ?>
                                    />
                                    <label for="tag_<?= $tagfilter ?>">
                                        <span class="list-label"><?= $tagfilter ?></span>
                                        <span class="counter tag_<?= $tagfilter ?>"><?= $count ?></span>
                                    </label>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </fieldset>




                <fieldset class="authors" data-default="<?= $opt->isdefault('authorfilter') && $opt->authorcompare() !== 'EMPTY' ? '1' : '0' ?>">
                    <legend>Author(s)</legend>
                            
                    <div>
                        <input type="radio" id="author_OR" name="authorcompare" value="OR" <?= $opt->authorcompare() == "OR" ? "checked" : "" ?>>
                        <label for="author_OR">OR</label>
                        <input type="radio" id="author_AND" name="authorcompare" value="AND" <?= $opt->authorcompare() == "AND" ? "checked" : "" ?>>
                        <label for="author_AND">AND</label>
                        <input type="radio" id="author_EMPTY" name="authorcompare" value="EMPTY" <?= $opt->authorcompare() == "EMPTY" ? "checked" : "" ?>>
                        <label for="author_EMPTY">EMPTY</label>
                        
                        <ul>
                            <?php foreach ($opt->authorlist() as $authorfilter => $count) : ?>

                                <li>
                                    <input
                                        type="checkbox"
                                        name="authorfilter[]"
                                        id="author_<?= $authorfilter ?>"
                                        value="<?= $authorfilter ?>"
                                        <?= in_array($authorfilter, $opt->authorfilter()) ? 'checked' : '' ?>
                                    />
                                    <label for="author_<?= $authorfilter ?>">
                                        <span class="list-label"><?= $authorfilter ?></span>
                                        <span class="counter"><?= $count ?></span>
                                    </label>
                                </li>
                            <?php endforeach ?>
                        </ul>   
                    </div>

                </fieldset>

                <fieldset data-default="<?= $opt->isdefault('since') && $opt->isdefault('until') ? '1' : '0' ?>">
                    <legend>Date & time</legend>
                    <div>
                        <label for="since">since</label>
                        <br>
                        <input type="datetime-local" name="since" id="since" value="<?= !is_null($opt->since()) ? $opt->since('Y-m-d\TH:i') : "" ?>">
                        <label for="until">until</label>
                        <br>
                        <input type="datetime-local" name="until" id="until" value="<?= !is_null($opt->until()) ? $opt->until('Y-m-d\TH:i') : "" ?>">
                    </div>
                </fieldset>


                <fieldset data-default="<?= $opt->isdefault('geo') ? '1' : '0' ?>">
                    <legend>geolocalisation</legend>
                    <div>
                        <input type="hidden" name="geo" value="0">
                        <input type="checkbox" name="geo" id="geo" value="1" <?= $opt->geo() ? 'checked' : '' ?>>
                        <label for="geo">page is geolocalized</label>
                    </div>
                </fieldset>




                <fieldset data-default="<?= $opt->isdefault('linkto') ? '1' : '0' ?>">
                    <legend>Link to</legend>
                    
                    <div>
                        <label for="linkto" title="filter pages that have links pointing to the following selected page">Link pointing to</label>
                        <br>
                        <select name="linkto" id="linkto">
                            <option value="" selected>-- not set --</option>
                            <?php
                            foreach ($opt->pageidlist() as $id) :
                                $selected = $id === $opt->linkto() ? ' selected': '';
                                ?>
                                <option value="<?= $id ?>" <?= $selected ?> ><?= $id ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </fieldset>


                <fieldset data-default="<?= $opt->isdefault('version') ? '1' : '0' ?>">
                    <legend>Version</legend>
                    <div>
                        <ul>
                            <li>
                                <input type="radio" id="version0" name="version" value="0" <?= $opt->version() === 0 ? "checked" : "" ?> />
                                <label for="version0">all</label>
                            </li>
                            <li>
                                <input type="radio" id="version1" name="version" value="<?= Wcms\Page::V1 ?>" <?= $opt->version() === Wcms\Page::V1  ? "checked" : "" ?> />
                                <label for="version1">v1</label>
                            </li>
                            <li>
                                <input type="radio" id="version2" name="version" value="<?= Wcms\Page::V2 ?>" <?= $opt->version() === Wcms\Page::V2  ? "checked" : "" ?> />
                                <label for="version2">v2</label>
                            </li>
                        </ul>
                    </div>
                </fieldset>

                

                <fieldset data-default="<?= $opt->isdefault('invert') ? '1' : '0' ?>">
                    <legend>Other</legend>

                    
                    <div>
                        <input type="hidden" name="invert" value="0" <?= !$opt->invert() ? 'checked' : '' ?>>
                        <input type="checkbox" name="invert" value="1" id="invert" <?= $opt->invert() ? 'checked' : '' ?>>

                        <label for="invert">invert</label><br>
                    </div>

                    
                </fieldset>

            </div>

            <input type="hidden" name="display" value="<?= $display ?>">


            <input type="submit" name="submit" value="filter" class="filter">
            <?php if ($opt->isfiltered()) : ?>
                <input type="submit" name="submit" value="reset">
            <?php endif ?>

        </form>
    </div>


</div>
