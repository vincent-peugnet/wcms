<nav>

    <div class="block">

        <h2>
            Options
        </h2>
        <div class="scroll">

        <details open>
            <summary>
                <i class="fa fa-gear"></i> filters
            </summary>


                <form action="./" method="get">
                    <input type="submit" name="submit" value="filter">
                    ⬅<input type="submit" name="submit" value="reset">

                    <div id="optfield">



                        <fieldset>
                            <legend>Sort</legend>
                            <select name="sortby" id="sortby">
                                <?php
                                foreach (Wcms\Model::COLUMNS as $col) {
                                    echo '<option value="' . $col . '" ' . ($opt->sortby() == $col ? "selected" : "") . '>' . $col . '</option>';
                                }
                                ?>
                            </select>
                            </br>
                            <input type="radio" id="asc" name="order" value="1" <?= $opt->order() == '1' ? "checked" : "" ?> /><label for="asc">ascending</label>
                            </br>
                            <input type="radio" id="desc" name="order" value="-1" <?= $opt->order() == '-1' ? "checked" : "" ?> /><label for="desc">descending</label>

                        </fieldset>




                        <fieldset data-default="<?= $opt->isdefault('secure') ? '1' : '0' ?>">
                            <legend>Privacy</legend>
                            <ul>
                                <li><input type="radio" id="4" name="secure" value="4" <?= $opt->secure() == 4 ? "checked" : "" ?> /><label for="4">all</label></li>
                                <li><input type="radio" id="2" name="secure" value="2" <?= $opt->secure() == 2 ? "checked" : "" ?> /><label for="2">not published</label></li>
                                <li><input type="radio" id="1" name="secure" value="1" <?= $opt->secure() == 1 ? "checked" : "" ?> /><label for="1">private</label></li>
                                <li><input type="radio" id="0" name="secure" value="0" <?= $opt->secure() == 0 ? "checked" : "" ?> /><label for="0">public</label></li>
                            </ul>
                        </fieldset>




                        <fieldset data-default="<?= $opt->isdefault('tagfilter') && $opt->tagcompare() != 'EMPTY' ? '1' : '0' ?>">
                            <legend>Tag</legend>

                            
                            <input type="radio" id="tag_OR" name="tagcompare" value="OR" ' . <?= $opt->tagcompare() == "OR" ? "checked" : "" ?> >
                            <label for="tag_OR">OR</label>
                            <input type="radio" id="tag_AND" name="tagcompare" value="AND" <?= $opt->tagcompare() == "AND" ? "checked" : "" ?>>
                            <label for="tag_AND">AND</label>
                            <input type="radio" id="tag_EMPTY" name="tagcompare" value="EMPTY" <?= $opt->tagcompare() == "EMPTY" ? "checked" : "" ?>>
                            <label for="tag_EMPTY">EMPTY</label>
                            
                            <ul>
                                <?php foreach ($opt->taglist() as $tagfilter => $count) { ?>
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
                                <?php } ?>
                            </ul>

                        </fieldset>




                        <fieldset data-default="<?= $opt->isdefault('authorfilter') ? '1' : '0' ?>">
                            <legend>Author(s)</legend>
                                    
                            
                            <input type="radio" id="author_OR" name="authorcompare" value="OR" ' . <?= $opt->authorcompare() == "OR" ? "checked" : "" ?>><label for="author_OR">OR</label>
                            <input type="radio" id="author_AND" name="authorcompare" value="AND" <?= $opt->authorcompare() == "AND" ? "checked" : "" ?>><label for="author_AND">AND</label>
                            
                            <ul>
                                <?php foreach ($opt->authorlist() as $authorfilter => $count) { ?>

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
                                            (<?= $count ?>)
                                        </label>
                                    </li>
                                <?php } ?>
                            </ul>

                        </fieldset>

                        <fieldset data-default="<?= $opt->isdefault('since') && $opt->isdefault('until') ? '1' : '0' ?>">
                            <legend>Date & time</legend>
                            <label for="since">since</label>
                            </br>
                            <input type="datetime-local" name="since" id="since" value="<?= !is_null($opt->since()) ? $opt->since('Y-m-d\TH:i') : "" ?>">
                            <label for="until">until</label>
                            </br>
                            <input type="datetime-local" name="until" id="until" value="<?= !is_null($opt->until()) ? $opt->until('Y-m-d\TH:i') : "" ?>">
                        </fieldset>




                        <fieldset data-default="<?= $opt->isdefault('linkto') ? '1' : '0' ?>">
                            <legend>Link to</legend>

                        <label for="linkto" title="filter pages that have links pointing to the following selected page">Link pointing to</label>
                        </br>
                            <select name="linkto" id="linkto">
                                <option value="" selected>-- not set --</option>
                                <?php
                                foreach ($opt->pageidlist() as $id) {
                                    if ($id === $opt->linkto()) {
                                        $selected = ' selected';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option value="' . $id . '"' . $selected . '>' . $id . '</option>';
                                }
                                ?>
                            </select>
                        </fieldset>


                        

                        <fieldset data-default="<?= $opt->isdefault('invert') && $opt->isdefault('limit') ? '1' : '0' ?>">
                            <legend>Other</legend>

                            

                        <?php
                        if ($opt->invert() == 1) {
                            echo '<input type="checkbox" name="invert" value="1" id="invert" checked>';
                        } else {
                            echo '<input type="checkbox" name="invert" value="1" id="invert">';
                        }
                        echo '<label for="invert">invert</></br>';
                        ?>
                            <input type="number" name="limit" id="limit" value="<?= $opt->limit() ?>" min="0" max="9999">
                            <label for="limit">limit</label>
                            
                        </fieldset>

                    </div>

                    <input type="hidden" name="display" value="<?= $display ?>">


                    <input type="submit" name="submit" value="filter">
                    ⬅<input type="submit" name="submit" value="reset">

                </form>

                
            </details>
        </div>
    </div>

</nav>