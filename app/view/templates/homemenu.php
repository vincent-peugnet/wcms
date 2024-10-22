<nav id="navbar" class="hbar">

    <div class="hbar-section">

<?php if($user->issupereditor()) : ?>
    <details id="json" class="dropdown">
        <summary>File</summary>
        <div class="dropdown-content">
            <form action="<?= $this->url('pageupload') ?>" method="post" enctype="multipart/form-data" class="dropdown-section">        
                <h2>Import page as file</h2>
                
                <input type="file" name="pagefile" id="pagefile" accept=".json" required>
                <label for="pagefile">JSON Page file</label>
                <input type="hidden" name="datecreation" value="0">
                <input type="hidden" name="author" value="0">
                <input type="hidden" name="erase" value="0">
                <br>
                <input type="text" name="id" id="id" placeholder="new id (optionnal)">
                <label for="id">change ID</label>
                <br>
                <input type="checkbox" name="datecreation" id="datecreation" value="1">
                <label for="datecreation">Reset date creation as now</label>
                <br>
                <input type="checkbox" name="author" id="author" value="1">
                <label for="author">Reset author(s) as just you</label>
                <br>
                <input type="checkbox" name="erase" id="erase" value="1">
                <label for="erase">Replace if already existing</label>
                <br>
                <input type="submit" value="upload">
            </form>
            <div class="dropdown-section">
                <h2>Cache</h2>
                <a href="<?= $this->url('flushrendercache') ?>">
                    <i class="fa fa-trash"></i>
                    Flush render cache
                </a>
            </div>
        </div>
    </details>



    <details id="edit" class="dropdown">
        <summary>Edit</summary>
        <form action="<?= $this->url('multi') ?>" method="post" id="multi" class="dropdown-content">
            <i>Edit selected pages</i>
            <div class="dropdown-section">

                <h2>Edit Meta infos</h2>
                <input type="text" name="datas[title]" id="title">
                <label for="title">title</label>
                <br>
                <input type="text" name="datas[description]" id="description">
                <label for="description">description</label>
                <br>
                <strong>Tag</strong>
                <br>
                <input type="hidden" name="reset[tag]" value="0">
                <input type="checkbox" name="reset[tag]" id="resettag" value="1">
                <label for="resettag">reset tag(s)</label>
                <br>
                <input type="text" name="addtag" id="addtag">
                <label for="addtag">add tag(s)</label>
                <br>
                <strong>Date</strong>
                <br>
                <input type="hidden" name="reset[date]" value="0">
                <input type="checkbox" name="reset[date]" id="resetdate" value="1">
                <label for="resetdate">reset date as now</label>
                <br>
                <input type="date" name="datas[pdate]" id="date">
                <label for="date">Date</label>
                <br>
                <input type="time" name="datas[ptime]" id="time">
                <label for="time">Time</label>
                <br>
                <strong>Privacy</strong>
                <br>
                <select name="datas[secure]" id="setlevel">
                    <option value=""  selected>--keep privacy--</option>
                    <option value="0">public</option>
                    <option value="1">private</option>
                    <option value="2">not_published</option>
                </select>
                <label for="setlevel">Privacy level</label>
                <br>
                <strong>Images</strong>
                <br>
                <select name="datas[favicon]" id="favicon">
                <option value=""  selected>--keep favicon--</option>
                <option value="!" >--unset favicon--</option>
                    <?php foreach ($faviconlist as $favicon) : ?>
                        <option value="<?= $favicon ?>"><?= $favicon ?></option>
                    <?php endforeach ?>
                </select>
                <label for="favicon">Favicon</label>
                <br>

                <select name="datas[thumbnail]" id="thumbnail">
                <option value="" selected>--keep thumbnail--</option>
                <option value="!">--unset thumbnail--</option>
                    <?php foreach ($thumbnaillist as $thumbnail) : ?>
                        <option value ="<?= $thumbnail ?>"><?= $thumbnail ?></option>
                    <?php endforeach ?>
                </select>
                <label for="thumbnail">Thumnail</label>
                <br>

                <strong>Geolocalisation</strong>
                <br>
                <input type="hidden" name="reset[geo]" value="0">
                <input type="checkbox" name="reset[geo]" id="resetgeo" value="1">
                <label for="resetgeo">delete geo datas</label>
                <br>

                <strong>Templates</strong>
                <br>
                <select name="datas[templatebody]" id="templatebody">
                    <option value="" selected>--keep template body--</option>
                    <option value="!" >--unset template body--</option>
                    <?php foreach ($pagelist as $page) : ?>
                        <option value ="<?= $page ?>"><?= $page ?></option>
                    <?php endforeach ?>
                </select>
                <label for="templatebody">Body</label>
                <br>

                <select name="datas[templatecss]" id="templatecss">
                    <option value="" selected>--keep css template--</option>
                    <option value="!" >--unset css template--</option>
                    <?php foreach ($pagelist as $page) : ?>
                        <option value ="<?= $page ?>"><?= $page ?></option>
                    <?php endforeach ?>                
                </select>
                <label for="templatecss">CSS</label>
                <br>

                <select name="datas[templatejavascript]" id="templatejavascript">
                    <option value="" selected>--keep javascript template--</option>
                    <option value="!" >--unset javascript template--</option>
                    <?php foreach ($pagelist as $page) :?>
                        <option value ="<?= $page ?>"><?= $page ?></option>
                    <?php endforeach ?>
                </select>
                <label for="templatejavascript">Javascript</label>
                <br>
                <strong>Advanced</strong>
                <br>
                <input type="number" name="datas[sleep]" id="sleep" min="0" max="180">
                <label for="sleep">Sleep time (seconds)</label>
                <br>
                <input type="hidden" name="reset[redirection]" value="0">
                <input type="checkbox" name="reset[redirection]" id="resetredirection" value="1">
                <label for="resetredirection">reset redirection</label>
                <br>
                <input type="text" name="datas[redirection]" id="redirection" list="searchdatalist">     
                <label for="redirection" title="page_id or URL like https://domain.org">Redirection</label>
                <br>
                <input type="number" name="datas[refresh]" id="refresh" min="0" max="180">
                <label for="refresh">refresh time (seconds)</label>
                <br>
                <strong>Author</strong>
                <br>
                <select name="addauthor" id="addauthor">
                    <option value="" disabled selected>--add author--</option>
                    <?php foreach ($editorlist as $editor) : ?>
                        <option value ="<?= $editor->id() ?>"><?= $editor->id() ?></option>
                    <?php endforeach ?>
                </select>
                <label for="addauthor">Author</label>
                <br>
                <input type="hidden" name="reset[author]" value="0">
                <input type="checkbox" name="reset[author]" id="resetauthor" value="1">
                <label for="resetauthor">Empty Authors</label>
                <br>
                <input type="hidden" name="reset[datemodif]" value="0">
                <input type="checkbox" name="reset[datemodif]" id="resetdatemodif" value="1" checked>
                <label for="resetdatemodif">update modification date</label>
                <br>

                <input type="submit" name="action" value="edit">
            </div>
            <div class="dropdown-section">
                <h2>Render</h2>
                <input type="submit" name="action" value="render">
            </div>
            <div class="dropdown-section">
                <h2>Delete</h2>
                <input type="hidden" name="confirmdelete" value="0">
                <input type="checkbox" name="confirmdelete" id="confirmdelete" value="1">
                <label for="confirmdelete">confirm</label>
                <input type="submit" name="action" value="delete">
            </div>
        </form>        
    </details>
<?php endif ?>









    <details id="selection" <?= !empty($optlist) ? 'open' : '' ?> class="dropdown">
        <summary>Filter</summary>
        <div class="dropdown-content">
            <form action="<?= $this->url('homequery') ?>" method="post" class="dropdown-section">
            <h2>
                List menu
                <span class="right">
                    <a href="<?= $this->url('info', [], '#page-list') ?>" title="help !" class="help">?</a>
                </span>
            </h2>
            <i>Generate code to display a list of pages</i>
            
                <input type="hidden" name="listquery" value="1">

                <select name="bookmark" id="list_bookmark">
                    <option value="">--no bookmark--</option>
                    <?php foreach ($matchedbookmarks as $bookmark) : ?>
                        <option
                            value="<?= $bookmark->id() ?>"
                            <?= !empty($optlist) && $optlist->bookmark() === $bookmark->id() ? 'selected' : '' ?>
                        >
                            <?= $bookmark->name() ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <label for="list_bookmark" title="use bookmark instead of filters">bookmark</label>
                <br>
                <input type="hidden" name="title" value="0">
                <input type="checkbox" name="title" id="list_title" value="1" <?= !empty($optlist) && !$optlist->title() ? '' : 'checked' ?>>
                <label for="list_title">Show title</label>
                <br>
                <input type="hidden" name="description" value="0">
                <input type="checkbox" name="description" id="list_description" value="1" <?= !empty($optlist) && $optlist->description() ? 'checked' : '' ?>>
                <label for="list_description">Show description</label>
                <br>
                <input type="hidden" name="thumbnail" value="0">
                <input type="checkbox" name="thumbnail" id="list_thumbnail" value="1" <?= !empty($optlist) && $optlist->thumbnail() ? 'checked' : '' ?>>
                <label for="list_thumbnail">Show thumbnail</label>
                <br>
                <input type="hidden" name="date" value="0">
                <input type="checkbox" name="date" id="list_date" value="1" <?= !empty($optlist) && $optlist->date() ? 'checked' : '' ?>>
                <label for="list_date">Show date</label>
                <br>
                <input type="hidden" name="time" value="0">
                <input type="checkbox" name="time" id="list_time" value="1" <?= !empty($optlist) && $optlist->time() ? 'checked' : '' ?>>
                <label for="list_time">Show time</label>
                <br>
                <input type="hidden" name="author" value="0">
                <input type="checkbox" name="author" id="list_author" value="1" <?= !empty($optlist) && $optlist->author() ? 'checked' : '' ?>>
                <label for="list_author">Show author(s)</label>
                <br>
                <input type="hidden" name="hidecurrent" value="0">
                <input type="checkbox" name="hidecurrent" id="list_hidecurrent" value="1" <?= !empty($optlist) && $optlist->hidecurrent() ? 'checked' : '' ?>>
                <label for="list_hidecurrent">Hide current page</label>
                <br>
                <select name="style" id="list_style">
                    <?= options(Wcms\Model::LIST_STYLES , !empty($optlist) ? $optlist->style() : null) ?>
                </select>
                <label for="list_style">style</label>
                <br>
                <input type="submit" value="generate">
            
            <?php if(!empty($optlist)) : ?>
                <code class="select-all"><?= $optlist->getcode() ?></code>
            <?php endif ?>
            </form>
            <div class="dropdown-section">
            <h2>
                Map
                <span class="right">
                    <a href="<?= $this->url('info', [], '#map') ?>" title="help !" class="help">?</a>
                </span>
            </h2>
            <i>A code to insert a map on a page</i>
            <?php if(!empty($optmap)) : ?>
                <code class="select-all"><?= $optmap->getcode() ?></code>
            <?php endif ?>
            </div>
            <div class="dropdown-section">
            <h2>
                Random page
                <span class="right">
                    <a href="<?= $this->url('info', [], '#random-page') ?>" title="help !" class="help">?</a>
                </span>
            </h2>
            <i>Generate a code to create a link to a random page using the current filtering options.</i>
            <?php if(!empty($optrandom)) : ?>
                <code class="select-all">[random page](<?= $optrandom->getcode() ?>)</code>
            <?php endif ?>
            </div>
        </div>
    </details>








    <details id="bookmark" class="dropdown">
        <summary>Bookmark</summary>
        <div class="dropdown-content">
            <?php if(empty($matchedbookmarks)) : ?>
                <h2>New bookmark</h2>
                <form action="<?= $this->url('bookmarkadd') ?>" method="post" class="dropdown-section">
                    <p>
                        Save those filters as a bookmark
                    </p>
                    <input type="hidden" name="route" value="home">
                    <input type="hidden" name="query" value="<?= $queryaddress ?>">
                    <input type="text" name="id" id="bookmark_id" required minlength="3">
                    <label for="bookmark_id">id</label>
                    <br>
                    <select name="icon" id="bookmark_icon">
                        <?php foreach (Wcms\Modelbookmark::BOOKMARK_ICONS as $icon) : ?>
                            <option value="<?= $icon ?>"><?= $icon ?></option>
                        <?php endforeach ?>
                    </select>
                    <label for="bookmark_icon">icon</label>
                    <br>
                    <select name="user" id="bookmark_type">
                        <?php if($user->isadmin()) : ?>
                            <option value="">public</option>
                        <?php endif ?>
                        <option value="<?= $user->id() ?>">personal</option>
                    </select>
                    <br>
                    <input type="submit" value="create">
                </form>
                <?php if(!empty($editablebookmarks)) : ?>
                    <form action="<?= $this->url('bookmarkupdate') ?>" method="post" class="dropdown-section">
                    <h2>Update existing bookmark</h2>
                    
                        <input type="hidden" name="query" value="<?= $queryaddress ?>">
                        <select name="id" id="bookmark_id" required>
                            <option value="" selected>--choose a bookmark--</option>
                            <?php foreach ($editablebookmarks as $id => $bookmark) : ?>
                                <option value="<?= $bookmark->id() ?>"><?= $bookmark->name() ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="bookmark_id">bookmark</label>
                        <br>
                        <input type="submit" value="update">
                    </form>
                <?php endif ?>
            <?php else : ?>
                <?php foreach ($matchedbookmarks as $bookmark) : ?>
                    <form action="<?= $this->url('bookmarkupdate') ?>" method="post" class="dropdown-section">
                    <?= $bookmark->icon() ?>
                    <strong>
                        <?= $bookmark->id() ?>
                    </strong>
                    <h2>Infos</h2>
                    
                        <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                        <select name="icon" id="bookmark_icon">
                            <?php foreach (Wcms\Modelbookmark::BOOKMARK_ICONS as $icon) : ?>
                                <option value="<?= $icon ?>" <?= $icon === $bookmark->icon() ? 'selected' : '' ?>><?= $icon ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="bookmark_icon">icon</label>
                        <br>
                        <input type="text" name="name" id="bookmark_name" value="<?= $bookmark->name() ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">
                        <label for="bookmark_name">name</label>
                        <br>
                        <input type="text" name="description" id="bookmark_description" value="<?= $bookmark->description() ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">
                        <label for="bookmark_description">description</label>
                        <br>
                        <?php if ($bookmark->ispublished()) : ?>
                            <select name="ref" id="bookmark_ref">
                                <option value="">--no ref page--</option>
                                <?php foreach ($pagelist as $page) : ?>
                                    <option value="<?= $page ?>" <?= $bookmark->ref() === $page ? 'selected' : '' ?>><?= $page ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="bookmark_ref">reference page</label>
                            <br>
                        <?php endif ?>
                        <input type="submit" value="update">
                    </form>
                    <?php if($bookmark->ispublic()) : ?>
                        <div class="dropdown-section">
                        <h2 title="you can use public bookmarks as RSS feeds">RSS feed</h2>
                        <?php if ($bookmark->ispublished()) : ?>
                            copy and paste this code in any page
                            <code class="select-all">%RSS?bookmark=<?= $bookmark->id() ?>%</code>
                            <a href="<?= $this->ubookmark('bookmarkpublish', $bookmark->id()) ?>" title="update the RSS feed">
                                <i class="fa fa-refresh"></i> refresh
                            </a>
                            <a href="<?= $this->ubookmark('bookmarkunpublish', $bookmark->id()) ?>">
                                <i class="fa fa-ban"></i> stop publishing
                            </a>
                        <?php else : ?>
                            <a href="<?= $this->ubookmark('bookmarkpublish', $bookmark->id()) ?>">
                                <i class="fa fa-rss"></i> publish !
                            </a>
                        <?php endif ?>
                        </div>
                    <?php endif ?>
                    <form action="<?= $this->url('bookmarkdelete') ?>" method="post" class="dropdown-section">
                    <h2>Delete</h2>
                    
                        <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                        <input type="hidden" name="route" value="home">
                        <input type="hidden" name="confirmdelete" value="0">
                        <input type="checkbox" name="confirmdelete" id="bookmark_confirmdelete" value="1">
                        <label for="bookmark_confirmdelete">confirm</label>
                        <br>
                        <input type="submit" value="delete">
                    </form>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </details>



    <details id="display" class="dropdown">
        <summary>Display</summary>
        <div class="dropdown-content">
            <form action="<?= $this->url('homecolumns') ?>" method="post" class="dropdown-section">
            <h2>Columns</h2>
            
            <input type="hidden" name="columns[]" value="id">
                <ul>
                    <li>
                        <input type="checkbox" name="columns[]" id="col_id" checked disabled>
                        <label for="col_id">id</label>
                    </li>
                <?php foreach (Wcms\User::HOME_COLUMNS as $col) :
                    $name = Wcms\Model::METADATAS_NAMES[$col];
                    ?>
                    <li>
                    <input type="checkbox" name="columns[]" value="<?= $col ?>" id="col_<?= $col ?>" <?= in_array($col, $user->columns()) ? 'checked' : '' ?>>
                    <label for="col_<?= $col ?>"><?= $name ?></label>
                    </li>
                <?php endforeach ?>
                </ul>
                <input type="submit" value="update columns">
            </form>

            <?php if($user->issupereditor()) : ?>
            <form action="<?= $this->url('homecolors') ?>" method="post" class="dropdown-section">
                <h2>Tag colors</h2>
            
                <ul>
                <?php foreach ($colors as $tag => $datas) : ?>
                    <li>
                        <input type="color" name="<?= $tag ?>" value="<?= $datas['background-color'] ?>" id="color_<?= $tag ?>">
                        <label for="color_<?= $tag ?>"><?= $tag ?></label>
                    </li>
                <?php endforeach ?>
                </ul>
                <input type="submit" value="update tag colors">
            </form>
            <?php endif ?>
        </div>
    </details>

    <span id="save-workspace">

<form
    action="<?= $this->url('workspaceupdate') ?>"
    method="post"
    data-api="<?= $this->url('apiworkspaceupdate') ?>"
    id="workspace-form"
>
    <input type="hidden" name="showeoptionspanel" value="0">
    <button type="submit">
        <i class="fa fa-edit"></i>
        <span class="text">save workspace</span>
    </button>
</form>
</span>

    </div>
</nav>
