<aside class="home">


<?php if($user->issupereditor()) { ?>
    <details class="hidephone" id="json">
        <summary>File</summary>
            <div class="submenu">
                <h2>Import page as file</h2>
            <form action="<?= $this->url('pageupload') ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="pagefile" id="pagefile" accept=".json">
            <label for="pagefile">JSON Page file</label>
            <input type="hidden" name="erase" value="0">
            <input type="hidden" name="datecreation" value="0">
            </br>
            <input type="text" name="id" id="id" placeholder="new id (optionnal)">
            <label for="id">change ID</label>
            </br>
            <input type="checkbox" name="datecreation" id="datecreation" value="1">
            <label for="datecreation">Reset date creation as now</label>
            </br>
            <input type="checkbox" name="author" id="author" value="1">
            <label for="author">Reset author(s) as just you</label>
            </br>
            <input type="checkbox" name="erase" id="erase" value="1">
            <label for="erase">Replace if already existing</label>
            </br>
            <input type="submit" value="upload">
            </form>
            <h2>Export database</h2>
            <p>Future feature</p>
            </div>
    </details>



    <details class="hidephone" id="edit">
        <summary>Edit</summary>
        <div class="submenu">
            <i>Edit selected pages</i>
            <form action="<?= $this->url('multi') ?>" method="post" id="multi">
            <h2>Edit Meta infos</h2>
            <input type="text" name="datas[title]" id="title">
                <label for="title">title</label>
                </br>
                <input type="text" name="datas[description]" id="description">
                <label for="description">description</label>
                </br>
                <strong>Tag</strong>
                </br>
                <input type="hidden" name="reset[tag]" value="0">
                <input type="checkbox" name="reset[tag]" id="resettag" value="1">
                <label for="resettag">reset tag(s)</label>
                </br>
                <input type="text" name="addtag" id="addtag">
                <label for="addtag">add tag(s)</label>
                </br>
                <strong>Date</strong>
                </br>
                <input type="hidden" name="reset[date]" value="0">
                <input type="checkbox" name="reset[date]" id="resetdate" value="1">
                <label for="resetdate">reset date as now</label>
                </br>
                <input type="date" name="datas[pdate]" id="date">
                <label for="date">Date</label>
                </br>
                <input type="time" name="datas[ptime]" id="time">
                <label for="time">Time</label>
                </br>
                <strong>Privacy</strong>
                </br>
                <select name="datas[secure]" id="setlevel">
                    <option value=""  disabled selected>--change privacy--</option>
                    <option value="0">public</option>
                    <option value="1">private</option>
                    <option value="2">not_published</option>
                </select>
                <label for="setlevel">Privacy level</label>
                </br>
                <strong>Favicon</strong>
                </br>
                <select name="datas[favicon]" id="favicon">
                <option value="" disabled selected>--set favicon--</option>
                    <?php
                        foreach ($faviconlist as $favicon) {
                            echo '<option value ="' . $favicon . '">' . $favicon . '</option>';
                        }
                    ?>
                </select>
                <label for="favicon">Favicon</label>
                </br>
                <strong>Templates</strong>
                </br>
                <select name="datas[templatebody]" id="templatebody">
                    <option value="" disabled selected>--set template body--</option>
                    <?php
                        foreach ($pagelist as $page) {
                            echo '<option value ="' . $page . '">' . $page . '</option>';
                        }
                    ?>
                </select>
                <label for="templatebody">Body</label>
                </br>
                <select name="datas[templatecss]" id="templatecss">
                <option value="" disabled selected>--set css template--</option>
                    <?php
                        foreach ($pagelist as $page) {
                            echo '<option value ="' . $page . '">' . $page . '</option>';
                        }
                    ?>                </select>
                <label for="templatecss">CSS</label>
                </br>
                <select name="datas[templatejavascript]" id="templatejavascript">
                <option value="" disabled selected>--set javascript template--</option>
                    <?php
                        foreach ($pagelist as $page) {
                            echo '<option value ="' . $page . '">' . $page . '</option>';
                        }
                    ?>                </select>
                <label for="templatejavascript">Javascript</label>
                </br>
                <strong>Advanced</strong>
                </br>
                <input type="number" name="datas[sleep]" id="sleep" min="0" max="180">
                <label for="sleep">Sleep time (seconds)</label>
                </br>
                <strong>Author</strong>
                </br>
                <select name="addauthor" id="addauthor">
                <option value="" disabled selected>--add author--</option>
                    <?php
                        foreach ($editorlist as $editor) {
                            echo '<option value ="' . $editor . '">' . $editor . '</option>';
                        }
                    ?>
                </select>
                <label for="addauthor">Author</label>
                </br>
                <input type="hidden" name="reset[author]" value="0">
                <input type="checkbox" name="reset[author]" id="resetauthor" value="1">
                <label for="resetauthor">Empty Authors</label>
                </br>
                <input type="hidden" name="reset[datemodif]" value="0">
                <input type="checkbox" name="reset[datemodif]" id="resetdatemodif" value="1">
                <label for="resetdatemodif">update modification date</label>
                </br>
                <input type="submit" name="action" value="edit">
                <h2>Actions</h2>
                <input type="submit" name="action" value="render">
                <input type="hidden" name="confirmdelete" value="0">
                <input type="checkbox" name="confirmdelete" id="confirmdelete" value="1">
                <label for="confirmdelete">confirm</label>
                <input type="submit" name="action" value="delete">
            </form>
        </div>
    </details>
    <?php } ?>









    <details class="hidephone" id="selection" <?= !empty($optlist) ? 'open' : '' ?>>
        <summary>Filters</summary>
        <div class="submenu">
        <h2>Get LIST code</h2>
        <i>Generate code to display a list of pages</i>
        <form action="<?= $this->url('homequery') ?>" method="post">
            <input type="hidden" name="query" value="1">

            <input type="hidden" name="title" value="0">
            <input type="checkbox" name="title" id="list_title" value="1" <?= !empty($optlist) && !$optlist->title() ? '' : 'checked' ?>>
            <label for="list_title">Show title</label>
            </br>
            <input type="hidden" name="description" value="0">
            <input type="checkbox" name="description" id="list_description" value="1" <?= !empty($optlist) && $optlist->description() ? 'checked' : '' ?>>
            <label for="list_description">Show description</label>
            </br>
            <input type="hidden" name="thumbnail" value="0">
            <input type="checkbox" name="thumbnail" id="list_thumbnail" value="1" <?= !empty($optlist) && $optlist->thumbnail() ? 'checked' : '' ?>>
            <label for="list_thumbnail">Show thumbnail</label>
            </br>
            <input type="hidden" name="date" value="0">
            <input type="checkbox" name="date" id="list_date" value="1" <?= !empty($optlist) && $optlist->date() ? 'checked' : '' ?>>
            <label for="list_date">Show date</label>
            </br>
            <input type="hidden" name="time" value="0">
            <input type="checkbox" name="time" id="list_time" value="1" <?= !empty($optlist) && $optlist->time() ? 'checked' : '' ?>>
            <label for="list_time">Show time</label>
            </br>
            <input type="hidden" name="author" value="0">
            <input type="checkbox" name="author" id="list_author" value="1" <?= !empty($optlist) && $optlist->author() ? 'checked' : '' ?>>
            <label for="list_author">Show author(s)</label>
            </br>
            <select name="style" id="list_style">
                <option value="0">list</option>
                <option value="1" <?= !empty($optlist) && $optlist->style() == 1 ? 'selected' : '' ?>>div</option>
            </select>
            <input type="submit" value="generate">
        </form>
        <?php if(!empty($optlist)) { ?>
            <code><?= $optlist->getcode() ?></code>
        <?php } ?>
        </div>
        </details>








    <details class="hidephone" id="bookmarks">
        <summary>Bookmarks</summary>
        <div class="submenu">
            <h2>Public</h2>
            <?php if(!empty(Wcms\Config::bookmark())) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
            <ul>
            <?php foreach (Wcms\Config::bookmark() as $id => $query) { ?>  
                <li>
                <label for="public-bookmark_<?= $id ?>">
                    <a href="<?= $query ?>" title="<?= $query ?>"><?= $id ?></a>
                    </label>
                    <?php if($user->issupereditor()) { ?>
                        <input type="checkbox" name="id[]" value="<?= $id ?>" id="public-bookmark_<?= $id ?>">
                    <?php } ?>
                </li>
            <?php } ?>
            </ul>
            <input type="hidden" name="action" value="del">
            <input type="submit" value="delete selected" class="floatright">
            </form>
            <?php } elseif($user->issupereditor()) { ?>
                <p>This will store your filters settings as a Bookmark that every editors users can use.</p>
            <?php } else { ?>
                <p>No public Bookmarks yet</p>
            <?php } ?>
            <?php if($user->issupereditor()) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
                <input type="text" name="id" placeholder="bookmark id" minlength="1" maxlength="16">
                <input type="hidden" name="query" value="<?= $opt->getadress() ?>">
                <input type="submit" name="action" value="add">
            </form>
            <?php } ?>
            <h2>Personnal</h2>
            <?php if(!empty($user->bookmark())) { ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
            <ul>
            <?php foreach ($user->bookmark() as $id => $query) { ?>  
                <li>
                    <a href="<?= $query ?>" title="<?= $query ?>"><?= $id ?></a>
                    <input type="checkbox" name="id[]" value="<?= $id ?>">
                </li>
            <?php } ?>
            </ul>
            <input type="hidden" name="action" value="del">
            <input type="hidden" name="user" value="<?= $user->id() ?>">
            <input type="submit" value="delete selected" class="floatright">
            </form>
            <?php } else { ?>
                <p>This will store your filters settings as a Bookmark that only you can use.</p>
            <?php } ?>
            <form action="<?= $this->url('homebookmark') ?>" method="post">
                <input type="text" name="id" placeholder="bookmark id" minlength="1" maxlength="16">
                <input type="hidden" name="query" value="<?= $opt->getadress() ?>">
                <input type="hidden" name="user" value="<?= $user->id() ?>">
                <input type="submit" name="action" value="add">
            </form>
        </div>
    </details>



    <details class="hidephone" id="display">
        <summary>Display</summary>
        <div class="submenu">
            <h2>Worksapce</h2>
        <form action="">
            <ul>
            <?php foreach ($user->display() as $id => $setting) { ?>
                <li>
                    <input type="checkbox" name="display[<?= $id ?>]" id="display_<?= $id ?>" value="true" <?= $setting ? 'checked' : '' ?>>
                    <label for="display_<?= $id ?>"><?= $id ?></label>
                </li>
            <?php } ?>
            </ul>
            <input type="submit" value="update display">
        </form>
            <h2>Columns</h2>
        <form action="<?= $this->url('homecolumns') ?>" method="post">
        <ul>
        <?php
        foreach (Wcms\Model::COLUMNS as $col) { ?>
            <li>
            <input type="checkbox" name="columns[]" value="<?= $col ?>" id="col_<?= $col ?>" <?= in_array($col, $user->columns()) ? 'checked' : '' ?>>
            <label for="col_<?= $col ?>"><?= $col ?></label>
            </li>
            <?php } ?>
        </ul>
        <input type="submit" value="update columns">
        </form>
        <?php if($user->issupereditor()) { ?>
        <h2>Tag colors</h2>
        <form action="<?= $this->url('homecolors') ?>" method="post">
            <?= $colors->htmlcolorpicker() ?>
            <input type="submit" value="update">
        </form>
        <?php } ?>
        </div>
    </details>



</aside>