<aside class="home">


<?php if($user->issupereditor()) { ?>
    <details id="json">
        <summary>File</summary>
            <div class="submenu">
                <h2>Import page as file</h2>
            <form action="<?= $this->url('pageupload') ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="pagefile" id="pagefile" accept=".json" required>
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



    <details id="edit">
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
                    <option value=""  selected>--keep privacy--</option>
                    <option value="0">public</option>
                    <option value="1">private</option>
                    <option value="2">not_published</option>
                </select>
                <label for="setlevel">Privacy level</label>
                </br>
                <strong>Images</strong>
                </br>
                <select name="datas[favicon]" id="favicon">
                <option value=""  selected>--keep favicon--</option>
                <option value="!" >--unset favicon--</option>
                    <?php
                        foreach ($faviconlist as $favicon) {
                            echo '<option value ="' . $favicon . '">' . $favicon . '</option>';
                        }
                    ?>
                </select>
                <label for="favicon">Favicon</label>
                </br>

                <select name="datas[thumbnail]" id="thumbnail">
                <option value="" selected>--keep thumbnail--</option>
                <option value="!">--unset thumbnail--</option>
                    <?php
                        foreach ($thumbnaillist as $thumbnail) {
                            echo '<option value ="' . $thumbnail . '">' . $thumbnail . '</option>';
                        }
                    ?>
                </select>
                <label for="thumbnail">Thumnail</label>
                </br>

                <strong>Templates</strong>
                </br>
                <select name="datas[templatebody]" id="templatebody">
                    <option value="" selected>--keep template body--</option>
                    <option value="!" >--unset template body--</option>
                    <?php
                        foreach ($pagelist as $page) {
                            echo '<option value ="' . $page . '">' . $page . '</option>';
                        }
                    ?>
                </select>
                <label for="templatebody">Body</label>
                </br>

                <select name="datas[templatecss]" id="templatecss">
                <option value="" selected>--keep css template--</option>
                <option value="!" >--unset css template--</option>
                    <?php
                        foreach ($pagelist as $page) {
                            echo '<option value ="' . $page . '">' . $page . '</option>';
                        }
                    ?>                </select>
                <label for="templatecss">CSS</label>
                </br>

                <select name="datas[templatejavascript]" id="templatejavascript">
                <option value="" selected>--keep javascript template--</option>
                <option value="!" >--unset javascript template--</option>
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
                <input type="hidden" name="reset[redirection]" value="0">
                <input type="checkbox" name="reset[redirection]" id="resetredirection" value="1">
                <label for="resetredirection">reset redirection</label>
                </br>
                <input type="text" name="datas[redirection]" id="redirection" list="searchdatalist">     
                <label for="redirection" title="page_id or URL like https://domain.org">Redirection</label>
                </br>
                <input type="number" name="datas[refresh]" id="refresh" min="0" max="180">
                <label for="refresh">refresh time (seconds)</label>
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
                <input type="checkbox" name="reset[datemodif]" id="resetdatemodif" value="1" checked>
                <label for="resetdatemodif">update modification date</label>
                </br>
                <input type="submit" name="action" value="edit">

                <h2>Render</h2>
                <input type="submit" name="action" value="render">

                <h2>Delete</h2>
                <input type="hidden" name="confirmdelete" value="0">
                <input type="checkbox" name="confirmdelete" id="confirmdelete" value="1">
                <label for="confirmdelete">confirm</label>
                <input type="submit" name="action" value="delete">

            </form>
        </div>
    </details>
    <?php } ?>









    <details id="selection" <?= !empty($optlist) ? 'open' : '' ?>>
        <summary>Filter</summary>
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
                <?= options(Wcms\Model::LIST_STYLES , !empty($optlist) ? $optlist->style() : null) ?>
            </select>
            <input type="submit" value="generate">
        </form>
        <?php if(!empty($optlist)) { ?>
            <input readonly class="code select-all" value="<?= $optlist->getcode() ?>" />
        <?php } ?>
        </div>
        </details>








    <details id="bookmark">
        <summary>Bookmark</summary>
        <div class="submenu">
            <?php if(empty($matchedbookmarks)) { ?>
            <h2>New bookmark</h2>
            <form action="<?= $this->url('bookmarkadd') ?>" method="post">
                <p>
                    Save those filters as a bookmark
                </p>
                <input type="hidden" name="route" value="home">
                <input type="hidden" name="query" value="<?= $queryaddress ?>">
                <input type="text" name="id" id="bookmark_id">
                <label for="bookmark_id">id</label>
                <br>
                <select name="icon" id="bookmark_icon">
                    <?php foreach (Wcms\Model::BOOKMARK_ICONS as $icon) { ?>
                        <option value="<?= $icon ?>"><?= $icon ?></option>
                    <?php } ?>
                </select>
                <label for="bookmark_icon">icon</label>
                <br>
                <input type="submit" value="create">
            </form>
            <?php } else { ?>
                <?php foreach ($matchedbookmarks as $bookmark) { ?>
                    <?= $bookmark->icon() ?>
                    <strong>
                        <?= $bookmark->id() ?>
                    </strong>
                    <h2>Update</h2>
                    <form action="<?= $this->url('bookmarkupdate') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                        <input type="hidden" name="route" value="<?= $bookmark->route() ?>">
                        <select name="icon" id="bookmark_icon">
                            <?php foreach (Wcms\Model::BOOKMARK_ICONS as $icon) { ?>
                                <option value="<?= $icon ?>" <?= $icon === $bookmark->icon() ? 'selected' : '' ?>><?= $icon ?></option>
                            <?php } ?>
                        </select>
                        <label for="bookmark_icon">icon</label>
                        <br>
                        <input type="text" name="name" id="bookmark_name" value="<?= $bookmark->name() ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">
                        <label for="bookmark_name">name</label>
                        <br>
                        <input type="text" name="description" id="bookmark_description" value="<?= $bookmark->description() ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">
                        <label for="bookmark_description">description</label>
                        <br>
                        <input type="submit" value="update">
                    </form>
                    <h2>Delete</h2>
                    <form action="<?= $this->url('bookmarkdelete') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                        <input type="hidden" name="route" value="<?= $bookmark->route() ?>">
                        <input type="hidden" name="confirmdelete" value="0">
                        <input type="checkbox" name="confirmdelete" id="bookmark_confirmdelete" value="1">
                        <label for="bookmark_confirmdelete">confirm</label>
                        <br>
                        <input type="submit" value="delete">
                    </form>
                <?php } ?>
            <?php } ?>
        </div>
    </details>



    <details id="display">
        <summary>Display</summary>
        <div class="submenu">
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
        <?php if($user->issupereditor() && !empty($colors)) { ?>
        <h2>Tag colors</h2>
        <form action="<?= $this->url('homecolors') ?>" method="post">
            <?= $colors->htmlcolorpicker() ?>
            <input type="submit" value="update">
        </form>
        <?php } ?>
        </div>
    </details>



</aside>