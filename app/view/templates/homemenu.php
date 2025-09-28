<nav id="navbar" class="hbar">

    <div class="hbar-section">

        <?php if($user->issupereditor()) : ?>

            <details name="menu" id="json" class="dropdown">
                <summary>File</summary>
                <div class="dropdown-content">
                    <form action="<?= $this->url('pageupload') ?>" method="post" enctype="multipart/form-data" class="dropdown-section">              
                        
                        <h3>Import page as file</h3>
                        <p class="field">
                            <label for="pagefile">JSON Page file</label>    
                            <input type="hidden" name="datecreation" value="0">
                            <input type="hidden" name="author" value="0">
                            <input type="hidden" name="erase" value="0">  
                            <input type="file" name="pagefile" id="pagefile" accept=".json" required>
                        </p>                    
                        <p class="field">
                            <label for="id">change ID</label>
                            <input type="text" name="id" id="id" placeholder="new id (optionnal)">
                        </p>
                        <p class="field">
                            <label for="datecreation">Reset date creation as now</label>
                            <input type="checkbox" name="datecreation" id="datecreation" value="1">
                        </p>
                        <p class="field">
                            <label for="author">Reset author(s) as just you</label>
                            <input type="checkbox" name="author" id="author" value="1">
                        </p>
                        <p class="field">
                            <label for="erase">Replace if already existing</label>
                            <input type="checkbox" name="erase" id="erase" value="1">
                        </p>
                        <p class="field submit-field">
                            <input type="submit" value="upload">
                        </p>
                    </form>

                    <div class="dropdown-section">
                        <h3>Cache</h3>
                        <p class="field submit-field">
                            <a href="<?= $this->url('flushrendercache') ?>" class="button">
                                <i class="fa fa-trash"></i> Flush render cache
                            </a>
                        </p>
                        <p class="field submit-field">
                            <a href="<?= $this->url('flushurlcache') ?>" class="button">
                                <i class="fa fa-trash"></i> Flush URL cache
                            </a>
                        </p>
                        <p class="field submit-field">
                            <a href="<?= $this->url('cleanurlcache') ?>" class="button">
                                <i class="fa fa-recycle"></i> Clean URL cache
                            </a>
                        </p>
                    </div>
                </div>

            </details>

            <details name="menu" id="edit" class="dropdown">
                <summary>Edit</summary>
                <form action="<?= $this->url('multi') ?>" method="post" id="multi" class="dropdown-content">
                    <i>Edit selected pages</i>        
                    <div class="dropdown-section">
                        <h3>Edit Meta infos</h3>
                        <p class="field">
                            <label for="title">title</label>
                            <input type="text" name="datas[title]" id="title">
                        </p>
                        <p class="field">
                            <label for="description">description</label>
                            <input type="text" name="datas[description]" id="description">
                        </p>

                        <h4>Tags</h4>
                        <p class="field">
                            <input type="hidden" name="reset[tag]" value="0">
                            <label for="resettag">reset tag(s)</label>
                            <input type="checkbox" name="reset[tag]" id="resettag" value="1">
                        </p>
                        <p class="field">
                            <label for="addtag">add tag(s)</label>
                            <input type="text" name="addtag" id="addtag">
                        </p>

                        <h4>Date</h4>
                        <p class="field">
                            <input type="hidden" name="reset[date]" value="0">
                            <label for="resetdate">reset date as now</label>
                            <input type="checkbox" name="reset[date]" id="resetdate" value="1">
                        </p>
                        <p class="field">
                            <input type="hidden" name="reset[datemodif]" value="0">
                            <label for="resetdatemodif">update modification date</label>
                            <input type="checkbox" name="reset[datemodif]" id="resetdatemodif" value="1" checked>
                        </p>
                        <div class="flexrow">
                            <p class="field">
                                <label for="date">Date</label>
                                <input type="date" name="datas[pdate]" id="date">
                            </p>
                            <p class="field">
                                <label for="time">Time</label>
                                <input type="time" name="datas[ptime]" id="time">
                            </p>
                        </div>

                        <h4>Privacy</h4>
                        <p class="field">
                            <label for="setlevel">Privacy level</label>
                            <select name="datas[secure]" id="setlevel">
                                <option value="" selected>--keep privacy--</option>
                                <option value="0">public</option>
                                <option value="1">private</option>
                                <option value="2">not_published</option>
                            </select>
                        </p>

                        <h4>Images</h4>
                        <p class="field">
                            <label for="favicon">Favicon</label>
                            <select name="datas[favicon]" id="favicon">
                                <option value="" selected>--keep favicon--</option>
                                <option value="!" >--unset favicon--</option>
                                <?php foreach ($faviconlist as $favicon) : ?>
                                    <option value="<?= $favicon ?>"><?= $favicon ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                        <p class="field">
                            <label for="thumbnail">Thumnail</label>
                            <select name="datas[thumbnail]" id="thumbnail">
                                <option value="" selected>--keep thumbnail--</option>
                                <option value="!">--unset thumbnail--</option>
                                <?php foreach ($thumbnaillist as $thumbnail) : ?>
                                    <option value ="<?= $thumbnail ?>"><?= $thumbnail ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>

                        <h4>Geolocalisation</h4>
                        <p class="field">
                            <input type="hidden" name="reset[geo]" value="0">
                            <label for="resetgeo">delete geo datas</label>
                            <input type="checkbox" name="reset[geo]" id="resetgeo" value="1">
                        </p>

                        <h4>Templates</h4>
                        <p class="field">
                            <label for="templatebody">Body</label>
                            <select name="datas[templatebody]" id="templatebody">
                                <option value="" selected>--keep template body--</option>
                                <option value="!" >--unset template body--</option>
                                <?php foreach ($pagelist as $page) : ?>
                                    <option value ="<?= $page ?>"><?= $page ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                        <p class="field">
                            <label for="templatecss">CSS</label>
                            <select name="datas[templatecss]" id="templatecss">
                                <option value="" selected>--keep css template--</option>
                                <option value="!" >--unset css template--</option>
                                <option value="%" >--same as body template--</option>
                                <?php foreach ($pagelist as $page) : ?>
                                    <option value ="<?= $page ?>"><?= $page ?></option>
                                <?php endforeach ?>            
                            </select>
                        </p>
                        <p class="field">
                            <label for="templatejavascript">Javascript</label>
                            <select name="datas[templatejavascript]" id="templatejavascript">
                                <option value="" selected>--keep javascript template--</option>
                                <option value="!" >--unset javascript template--</option>
                                <option value="%" >--same as body template--</option>
                                <?php foreach ($pagelist as $page) :?>
                                    <option value ="<?= $page ?>"><?= $page ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>

                        <h4>Advanced</h4>
                        <p class="field">
                            <label for="keepindex">keep index setting</label>
                            <input type="radio" name="datas[noindex]" id="keepindex" value="" checked>
                        </p>
                        <p class="field" title="prevent public pages being indexed by search engines">
                            <label for="noindex">no index</label>
                            <input type="radio" name="datas[noindex]" id="noindex" value="1">
                        </p>
                        <p class="field">
                            <label for="index">allow index</label>
                            <input type="radio" name="datas[noindex]" id="index" value="0">
                        </p>
                        <div class="flexrow">
                            <p class="field">
                                <label for="sleep">Sleep (s.)</label>
                                <input type="number" name="datas[sleep]" id="sleep" min="0" max="180">
                            </p>
                            <p class="field">
                            <label for="refresh">Refresh (s.)</label>
                                <input type="number" name="datas[refresh]" id="refresh" min="0" max="180">
                            </p>
                        </div>
                        <p class="field">
                            <label for="resetredirection">reset redirection</label>
                            <input type="hidden" name="reset[redirection]" value="0">
                            <input type="checkbox" name="reset[redirection]" id="resetredirection" value="1">
                        </p>
                        <p class="field">
                            <label for="redirection" title="page_id or URL like https://domain.org">Redirection</label>
                            <input type="text" name="datas[redirection]" id="redirection" list="searchdatalist">     
                        </p>
                        
                        <h4>Authors</h4> 
                        <p class="field">
                            <label for="addauthor">Author</label>
                            <select name="addauthor" id="addauthor">
                                <option value="" disabled selected>--add author--</option>
                                <?php foreach ($editorlist as $editor) : ?>
                                    <option value ="<?= $editor->id() ?>"><?= $editor->id() ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                        <p class="field">
                            <input type="hidden" name="reset[author]" value="0">
                            <label for="resetauthor">Empty Authors</label>
                            <input type="checkbox" name="reset[author]" id="resetauthor" value="1">
                        </p>

                        <p class="field submit-field">
                            <input type="submit" name="action" value="edit">
                        </p>
                    </div>
                    
                    <div class="dropdown-section">
                        <h3>Render</h3>
                        <p class="field submit-field">
                            <input type="submit" name="action" value="render">
                        </p>
                    </div>

                    <div class="dropdown-section">                
                        <h3>Delete</h3>
                        <div class="flexrow">
                            <input type="hidden" name="confirmdelete" value="0">
                            <p class="field">                        
                                <label for="confirmdelete">confirm</label>    
                                <input type="checkbox" name="confirmdelete" id="confirmdelete" value="1">                        
                            </p>
                            <p class="field submit-field">                        
                                <input type="submit" name="action" value="delete">
                            </p>
                        </div>
                    </div>

                </form>

            </details>

        <?php endif ?>

        <details name="menu" id="selection" <?= !empty($optlist) ? 'open' : '' ?> class="dropdown">
            <summary>Filter</summary>
            <div class="dropdown-content">
                <form action="<?= $this->url('homequery') ?>" method="post" class="dropdown-section">
                    <h3>
                        List menu
                        <a href="<?= $this->url('info', [], '#page-list') ?>" title="help !" class="help">?</a>
                    </h3>
                    <i>Generate code to display a list of pages</i>
                
                    <input type="hidden" name="listquery" value="1">

                    <p class="field">
                        <label for="list_bookmark" title="use bookmark instead of filters">Bookmark</label>
                        <select name="bookmark" id="list_bookmark">
                            <option value="">--no bookmark--</option>
                            <?php foreach ($matchedbookmarks as $bookmark) : ?>
                            <option value="<?= $bookmark->id() ?>" <?= !empty($optlist) && $optlist->bookmark() === $bookmark->id() ? 'selected' : '' ?> >
                                <?= $this->e($bookmark->name()) ?>
                            </option>
                        <?php endforeach ?>
                        </select>
                    </p>

                    <p class="field">
                        <input type="hidden" name="title" value="0">
                        <label for="list_title">Show title</label>
                        <input type="checkbox" name="title" id="list_title" value="1" <?= !empty($optlist) && !$optlist->title() ? '' : 'checked' ?>>
                    </p>
                    <p class="field">
                        <input type="hidden" name="description" value="0">
                        <label for="list_description">Show description</label>
                        <input type="checkbox" name="description" id="list_description" value="1" <?= !empty($optlist) && $optlist->description() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">
                        <input type="hidden" name="thumbnail" value="0">
                        <label for="list_thumbnail">Show thumbnail</label>
                        <input type="checkbox" name="thumbnail" id="list_thumbnail" value="1" <?= !empty($optlist) && $optlist->thumbnail() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">
                        <input type="hidden" name="date" value="0">
                        <label for="list_date">Show date</label>
                        <input type="checkbox" name="date" id="list_date" value="1" <?= !empty($optlist) && $optlist->date() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">
                        <input type="hidden" name="time" value="0">
                        <label for="list_time">Show time</label>
                        <input type="checkbox" name="time" id="list_time" value="1" <?= !empty($optlist) && $optlist->time() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">                
                        <input type="hidden" name="author" value="0">
                        <label for="list_author">Show author(s)</label>
                        <input type="checkbox" name="author" id="list_author" value="1" <?= !empty($optlist) && $optlist->author() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">                
                        <input type="hidden" name="hidecurrent" value="0">
                        <label for="list_hidecurrent">Hide current page</label>
                        <input type="checkbox" name="hidecurrent" id="list_hidecurrent" value="1" <?= !empty($optlist) && $optlist->hidecurrent() ? 'checked' : '' ?>>
                    </p>
                    <p class="field">                
                        <label for="list_style">Style</label>                
                        <select name="style" id="list_style">
                            <?= options(Wcms\Optlist::STYLES , !empty($optlist) ? $optlist->style() : null) ?>
                        </select>
                    </p>
                    <p class="field submit-field">
                        <input type="submit" value="generate">
                    </p>
                    <?php if(!empty($optlist)) : ?>
                        <code class="select-all"><?= $optlist->getcode() ?></code>
                    <?php endif ?>
                </form>

                <div class="dropdown-section">
                    <h3>
                        Map
                        <a href="<?= $this->url('info', [], '#map') ?>" title="help !" class="help">?</a>                        
                    </h3>
                    <i>A code to insert a map on a page</i>
                    <?php if(!empty($optmap)) : ?>
                        <code class="select-all"><?= $optmap->getcode() ?></code>
                    <?php endif ?>
                </div>

                <div class="dropdown-section">
                    <h3>
                        Random page
                        <a href="<?= $this->url('info', [], '#random-page') ?>" title="help !" class="help">?</a>
                    </h3>
                    <i>Generate a code to create a link to a random page using the current filtering options.</i>
                    <?php if(!empty($optrandom)) : ?>
                        <code class="select-all">[random page](<?= $optrandom->getcode() ?>)</code>
                    <?php endif ?>
                </div>
            </div>
        </details>



        <details name="menu" id="bookmark" class="dropdown">
            <summary>Bookmark</summary>
            <div class="dropdown-content">
                <?php if(empty($matchedbookmarks)) : ?>
                    <form action="<?= $this->url('bookmarkadd') ?>" method="post" class="dropdown-section">
                        <h3>New bookmark</h3>
                        <p>
                            Save those filters as a bookmark
                        </p>
                        <input type="hidden" name="route" value="home">
                        <input type="hidden" name="query" value="<?= $queryaddress ?>">
                        <p class="field">
                            <label for="bookmark_id">id</label>
                            <input type="text" name="id" id="bookmark_id" required minlength="3">
                        </p>                                       
                        <p class="field">
                            <label for="bookmark_icon">icon</label>
                            <select name="icon" id="bookmark_icon">
                                <?php foreach (Wcms\Modelbookmark::BOOKMARK_ICONS as $icon) : ?>
                                    <option value="<?= $icon ?>"><?= $icon ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                        <p class="field">
                            <label for="bookmark_type">type</label>
                            <select name="user" id="bookmark_type">
                                <?php if($user->isadmin()) : ?>
                                    <option value="">public</option>
                                <?php endif ?>
                                <option value="<?= $user->id() ?>">personal</option>
                            </select>
                        </p>
                        <p class="field submit">
                            <input type="submit" value="create bookmark">
                        </p>
                    </form>

                    <?php if(!empty($editablebookmarks)) : ?>
                        <form action="<?= $this->url('bookmarkupdate') ?>" method="post" class="dropdown-section">
                            <h3>Update existing bookmark</h3>
                        
                            <input type="hidden" name="query" value="<?= $queryaddress ?>">
                            <p class="field">
                                <label for="bookmark_id">bookmark</label>
                                <select name="id" id="bookmark_id" required>
                                    <option value="" selected>--choose a bookmark--</option>
                                    <?php foreach ($editablebookmarks as $id => $bookmark) : ?>
                                        <option value="<?= $bookmark->id() ?>"><?= $this->e($bookmark->name()) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </p>
                            <p class="field submit">                        
                                <input type="submit" value="update">
                            </p>
                        </form>
                    <?php endif ?>
                <?php else : ?>
                    <?php foreach ($matchedbookmarks as $bookmark) : ?>
                        <form action="<?= $this->url('bookmarkupdate') ?>" method="post" class="dropdown-section">
                            <h3>
                                <strong>
                                    <?= $bookmark->icon() ?>    
                                    <?= $bookmark->id() ?>
                                </strong>
                            </h3>
                            <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                            <p class="field">       
                                <label for="bookmark_icon">icon</label>
                                <select name="icon" id="bookmark_icon">
                                    <?php foreach (Wcms\Modelbookmark::BOOKMARK_ICONS as $icon) : ?>
                                        <option value="<?= $icon ?>" <?= $icon === $bookmark->icon() ? 'selected' : '' ?>><?= $icon ?></option>
                                    <?php endforeach ?>
                                </select>
                            </p>
                            <p class="field">       
                                <label for="bookmark_name">name</label>
                                <input type="text" name="name" id="bookmark_name" value="<?= $this->e($bookmark->name()) ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">                        
                            </p>
                            <p class="field">       
                                <label for="bookmark_description">description</label>
                                <input type="text" name="description" id="bookmark_description" value="<?= $this->e($bookmark->description()) ?>" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">                        
                            </p>
                            <?php if ($bookmark->ispublished()) : ?>
                                <p class="field">       
                                    <label for="bookmark_ref">reference page</label>
                                    <select name="ref" id="bookmark_ref">
                                        <option value="">--no ref page--</option>
                                        <?php foreach ($pagelist as $page) : ?>
                                            <option value="<?= $page ?>" <?= $bookmark->ref() === $page ? 'selected' : '' ?>><?= $page ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </p>
                            <?php endif ?>
                            <p class="field submit-field">
                                <input type="submit" value="update">
                            </p>
                        </form>

                        <?php if($bookmark->ispublic()) : ?>
                            <div class="dropdown-section">
                                <h3 title="you can use public bookmarks as RSS feeds">RSS feed</h3>
                                <?php if ($bookmark->ispublished()) : ?>
                                    copy and paste this code in any page
                                    <code class="select-all">%RSS?bookmark=<?= $bookmark->id() ?>%</code>
                                    <p class="field submit-field">
                                        <a href="<?= $this->ubookmark('bookmarkpublish', $bookmark->id()) ?>" title="update the RSS feed" class="button">
                                            <i class="fa fa-refresh"></i> refresh
                                        </a>
                                    </p>
                                    <p class="field submit-field">
                                        <a href="<?= $this->ubookmark('bookmarkunpublish', $bookmark->id()) ?>" class="button">
                                            <i class="fa fa-ban"></i> stop publishing
                                        </a>
                                    </p>
                                <?php else : ?>
                                    <p class="field submit-field">
                                        <a href="<?= $this->ubookmark('bookmarkpublish', $bookmark->id()) ?>" class="button">
                                            <i class="fa fa-rss"></i> publish !
                                        </a>
                                    </p>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                        
                        <form action="<?= $this->url('bookmarkdelete') ?>" method="post" class="dropdown-section">
                            <h3>Delete</h3>
                        
                            <input type="hidden" name="id" value="<?= $bookmark->id() ?>">
                            <input type="hidden" name="route" value="home">
                            <input type="hidden" name="confirmdelete" value="0">
                            <dif class="flexrow">
                                <p class="field">
                                    <label for="bookmark_confirmdelete">confirm</label>    
                                    <input type="checkbox" name="confirmdelete" id="bookmark_confirmdelete" value="1">
                                </p>
                                <p class="field submit-field">
                                    <input type="submit" value="delete">
                                </p>
                            </dif>
                        </form>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
        </details>



        <details name="menu" id="display" class="dropdown">
            <summary>Display</summary>
            <div class="dropdown-content">
                <form action="<?= $this->url('homecolumns') ?>" method="post" class="dropdown-section">
                    <h3>Columns</h3>
                                    
                    <input type="hidden" name="columns[]" value="id">
                    <p class="field">
                        <label for="col_id">id</label>
                        <input type="checkbox" name="columns[]" id="col_id" checked disabled>
                    </p>
                    <?php foreach (Wcms\User::HOME_COLUMNS as $col) : ?>
                        <p class="field">
                            <label for="col_<?= $col ?>"><?= Wcms\Model::METADATAS_NAMES[$col] ?></label>
                            <input type="checkbox" name="columns[]" value="<?= $col ?>" id="col_<?= $col ?>" <?= in_array($col, $user->columns()) ? 'checked' : '' ?>>                
                        </p>
                    <?php endforeach ?>
                    <p class="field submit-field">
                        <input type="submit" value="update columns">
                    </p>
                </form>

                <?php if($user->issupereditor()) : ?>
                    <form action="<?= $this->url('homecolors') ?>" method="post" class="dropdown-section">
                        <h3>Tag colors</h3>
                        <?php foreach ($colors as $tag => $datas) : ?>
                            <p class="field">
                                <input type="color" name="<?= $tag ?>" value="<?= $datas['background-color'] ?>" id="color_<?= $tag ?>">
                                <label for="color_<?= $tag ?>"><?= $tag ?></label>
                            </p>
                        <?php endforeach ?>
                        <p class="field submit-field">
                            <input type="submit" value="update tag colors">
                        </p>
                    </form>
                <?php endif ?>
            </div>
        </details>
    </div>

    <div class="hbar-section">

        <div id="save-workspace">
            <form
                action="<?= $this->url('workspaceupdate') ?>"
                method="post"
                data-api="<?= $this->url('apiworkspaceupdate') ?>"
                id="workspace-form"
            >
                <input type="hidden" name="route" value="home">
                <input type="hidden" name="showhomeoptionspanel" value="0">
                <input type="hidden" name="showhomebookmarkspanel" value="0">
                <button type="submit">
                    <i class="fa fa-edit"></i>
                    <span class="text">save workspace</span>
                </button>
            </form>
        </div>
    </div>
    
</nav>
