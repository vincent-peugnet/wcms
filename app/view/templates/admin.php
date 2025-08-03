<?php $this->layout('backlayout', ['title' => 'admin', 'theme' => $theme, 'stylesheets' => [
    Wcms\Model::jspath() . 'admin.bundle.css',
    $css . 'back.css',
    $css . 'tagify.css',
    $css . 'admin.css',
    $css . 'tagcolors.css',
]]) ?>
<?php $this->start('page') ?>
<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin', 'pagelist' => $pagelist]) ?>

<nav id="navbar" class="hbar">
    <form action="<?= $this->url('adminupdate') ?>" method="post" id="admin" class="hbar-section">            
        <button type="submit" accesskey="s" >
            <i class="fa fa-save"></i>
            <span>Save</span>
        </button>
    </form>
    <div class="hbar-section">
        <a href="<?= $this->url('adminlog') ?>#bottom">
            <i class="fa fa-align-left"></i>
            logs
        </a>
    </div>
</nav>

<main class="admin grid">

    <div class="grid-item"  id="home-page">
        <h2>Visitor interface</h2>

        <h3>Landing page</h3>

        <p class="info">
            By default, W has no landing page for visitors without an account.
            But if you wish, you can define a page to which visitors who are not logged in will be redirected.
        </p>

        <p class="field">
            <label for="default">default</label>    
            <input type="radio" name="homepage" value="default" id="default" <?= Wcms\Config::homepage() === 'default' ? 'checked' : '' ?> form="admin">                        
        </p>

        <p class="field">
            <label for="redirect">redirect to page</label>
            <input type="radio" name="homepage" value="redirect" id="redirect" <?= Wcms\Config::homepage() === 'redirect' ? 'checked' : '' ?> form="admin">
        </p>

        <p class="field">
            <select name="homeredirect" id="homeredirect" form="admin">
                <option value="" <?= Wcms\Config::homeredirect() === null ? 'selected' : '' ?>>--select page to redirect--</option>
                <?php foreach ($pagelist as $page) : ?>
                    <option value="<?= $page ?>" <?= Wcms\Config::homeredirect() === $page ? 'selected' : '' ?>><?= $page ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <h3>help button</h3>

        <p class="info">
            Add a "help" button on login pages that point to a page or an URL.
            Leave empty if you don't need one.
        </p>

            
        <p class="field">
            <label for="helpbutton">destination</label>
            <input
                type="text"
                name="helpbutton"
                form="admin"
                id="helpbutton"
                value="<?= $this->e(Wcms\Config::helpbutton()) ?>"
                list="searchdatalist"
                placeholder="URL or page ID"
                maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>"
            >
        </p>


    </div>

    <div class="grid-item"  id="databases">
        
        <h2>Databases</h2>

        <h3>Used database</h3>

        <form action="<?= $this->url('admindatabase') ?>" method="post">
            <table id="dirlsit" >
                <tr><th>using</th><th>databases</th><th>pages</th></tr>                            
                <?php foreach($pagetables as $folder) : ?>
                    <tr>
                        <td>
                            <input
                                type="radio"
                                name="pagetable"
                                value="<?= $folder->name ?>"
                                id="db_<?= $folder->name ?>"
                                <?= $folder->selected ? 'checked' : '' ?>
                            >
                        </td>
                        <td>
                            <label for="db_<?= $folder->name ?>"><?= $folder->name ?></label>
                        </td>
                        <td>
                            <?= $folder->filecount ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>

            <p class="field submit-field">
                <input type="hidden" name="action" value="select">
                <input type="submit" value="select" name="change database">
            </p>
        </form>

        <h3>Duplicate database</h3>

        <form action="<?= $this->url('admindatabase') ?>" method="post">

            <p class="field">
                <label for="dbsrc">Database to duplicate</label>
                <select name="dbsrc" id="dbsrc">
                    <?php foreach ($pagetables as $table) : ?>
                        <option value="<?= $table->name ?>" <?= $table->selected ? 'selected' : '' ?>><?= $table->name ?></option>
                    <?php endforeach ?>
                </select>
            </p>

            <p class="field">
                <label for="duplicate">New name</label>
                <input type="text" name="dbtarget" id="duplicate" value="" required>
            </p>
            <p class="field submit-field">
                <input type="submit" name="action" value="duplicate">
            </p>
        </form>

    </div>

    <div class="grid-item"  id="interface">
        <h2 id="interface">Interface</h2>

        <h3>Theme</h3>

        <p class="info">See <a href="<?= $this->url('info', [], '#themes') ?>">📖 manual section</a> for more infos.
        </p>

        <p class="field">
            <label for="theme">select interface theme</label>
            <select name="theme" id="theme" form="admin">
                <?php foreach ($themes as $theme) : ?>
                    <option value="<?= $theme ?>" <?= $theme === Wcms\Config::theme() ? 'selected' : '' ?>><?= $theme ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <h3>Javascript</h3>

        <p class="info">Disables javascript in the user interface.
            Syntax highlighting, depend on it and will therefore not be displayed.
            This also reduces comfort a little, but full functionality is retained.
        </p>

        <p class="field">
            <label for="disablejavascript">Disable javascript</label>
            <input type="hidden" name="disablejavascript" value="0" form="admin">
            <input type="checkbox" name="disablejavascript" id="disablejavascript" value="1" <?= Wcms\Config::disablejavascript() ? 'checked' : '' ?> form="admin">                    
        </p>

    </div>

    <div class="grid-item"  id="page-creation">

        <h2>Page creation</h2>

        <h3>Page version</h3>

        <p class="info">Choose W page version you want to use when a new page is created.
        </p>

        <p class="field">
            <label for="pageversion">Select page version</label>
            <select name="pageversion" id="pageversion" form="admin">
                <?= options(Wcms\Page::VERSIONS, Wcms\Config::pageversion()) ?>
            </select>
        </p>

        
        <?php if (Wcms\Config::pageversion() > 1) : ?>
            <h3>Default content</h3>
            <p class="field">
                <label for="defaultcontent">Edit default page content</label>
                <textarea name="defaultcontent" id="defaultcontent" rows="6" spellcheck="false" form="admin"><?= $this->e(Wcms\Config::defaultcontent()) ?></textarea>
            </p>
        <?php endif ?>

        <h3>Default BODY</h3>

        <?php $defaultbody = 'defaultv' . Wcms\Config::pageversion() . 'body' ?>
        <p class="field">
            <label for="defaultbody">Edit default page V<?= Wcms\Config::pageversion() ?> BODY content</label>
            <textarea name="<?= $defaultbody ?>" id="defaultbody" rows="6" spellcheck="false" form="admin"><?= $this->e(Wcms\Config::defaultbody()) ?></textarea>
        </p>
        
        <h3>Privacy of new pages</h3>

        <p class="field">
            <label for="defaultprivacy">Default privacy</label>
            <select name="defaultprivacy" id="defaultprivacy" form="admin">
                <option value="0" <?= Wcms\Config::defaultprivacy() == 0 ? 'selected' : '' ?>>public</option>
                <option value="1" <?= Wcms\Config::defaultprivacy() == 1 ? 'selected' : '' ?>>private</option>
                <option value="2" <?= Wcms\Config::defaultprivacy() == 2 ? 'selected' : '' ?>>not published</option>
            </select>
        </p>

        <h3>Default tags</h3>

        <p class="field">
            <label for="defaulttag">Tag(s)</label>
            <input type="text" name="defaulttag" id="defaulttag" value="<?= Wcms\Config::defaulttag('string'); ?>" form="admin">
        </p>

        <h3>Default templates</h3>

        <p class="field">
            <label for="defaulttemplatebody">BODY template</label>
            <select name="defaulttemplatebody" id="defaulttemplatebody" form="admin">
                <option value="" <?= Wcms\Config::defaulttemplatebody() === "" ? 'selected' : '' ?>>--no default body template--</option>
                <?php foreach ($pagelist as $page) : ?>
                    <option value="<?= $page ?>" <?= Wcms\Config::defaulttemplatebody() === $page ? 'selected' : '' ?>><?= $page ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p class="field">
            <label for="defaulttemplatecss">CSS template</label>
            <select name="defaulttemplatecss" id="defaulttemplatecss" form="admin">
                <option value="%" <?= Wcms\Config::defaulttemplatecss() === null ? 'selected' : '' ?>>--same as default body template--</option>
                <option value="" <?= Wcms\Config::defaulttemplatecss() === '' ? 'selected' : '' ?>>--no default css template--</option>
                <?php foreach ($pagelist as $page) : ?>
                    <option value="<?= $page ?>" <?= Wcms\Config::defaulttemplatecss() === $page ? 'selected' : '' ?>><?= $page ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p class="field">
            <label for="defaulttemplatejavascript">JS template</label>
            <select name="defaulttemplatejavascript" id="defaulttemplatejavascript" form="admin">
                <option value="%" <?= Wcms\Config::defaulttemplatejavascript() === null ? 'selected' : '' ?>>--same as default body template--</option>
                <option value="" <?= Wcms\Config::defaulttemplatejavascript() === '' ? 'selected' : '' ?>>--no default js template--</option>
                <?php foreach ($pagelist as $page) : ?>
                    <option value="<?= $page ?>" <?= Wcms\Config::defaulttemplatejavascript() === $page ? 'selected' : '' ?>><?= $page ?></option>
                <?php endforeach ?>
            </select>
        </p>

    </div>

    <div class="grid-item"  id="alert-pages">
    
        <h2>Alert pages</h2>

        <p class="info">Set the style and text to show when a page does not exist, or when a visitor don't have access to it.
        </p>

        <h3>Common options</h3>

        <p class="field">
            <label for="alerttitle">H1 Title</label>
            <input type="text" name="alerttitle" id="alerttitle" value="<?= $this->e(Wcms\Config::alerttitle()) ?>" form="admin">
        </p>

        <p class="field">
            <label for="alertlink">Link to this page (for visitors)</label>
            <select name="alertlink" id="alertlink" form="admin">
                <option value="" <?= empty(Wcms\Config::alertlink()) ? 'selected' : '' ?>>--No link--</option>
                <?php foreach ($pagelist as $page) : ?>
                    <option value="<?= $page ?>" <?= Wcms\Config::alertlink() === $page ? 'selected' : '' ?>><?= $page ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p class="field">
            <label for="alertlinktext">Link text</label>
            <input type="text" name="alertlinktext" id="alertlinktext" value="<?= $this->e(Wcms\Config::alertlinktext()) ?>" form="admin">
        </p>

        <h3>Un-existing</h3>

        <p class="field">
            <label for="existnot">Text to show when a page does not exist yet. (This will also be shown as a tooltip over links.)</label>
            <input type="text" name="existnot" id="existnot" value="<?= $this->e(Wcms\Config::existnot()) ?>" form="admin">
        </p>

        <p class="field">
            <input type="hidden" name="existnotpass" value="0" form="admin">
            <label for="existnotpass">Ask for password</label>
            <input type="checkbox" name="existnotpass" id="existnotpass" value="1" <?= Wcms\Config::existnotpass() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>Private</h3>

        <p class="field">
            <label for="private">Text to show when a page is private.</label>
            <input type="text" name="private" id="private" value="<?= $this->e(Wcms\Config::private()) ?>" form="admin">
        </p>

        <p class="field">
            <input type="hidden" name="privatepass" value="0" form="admin">
            <label for="privatepass">Ask for password</label>
            <input type="checkbox" name="privatepass" id="privatepass" value="1" <?= Wcms\Config::privatepass() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>Not published</h3>

        <p class="field">
            <label for="notpublished">Text to show when a page is not published.</label>
            <input type="text" name="notpublished" id="notpublished" value="<?= $this->e(Wcms\Config::notpublished()) ?>" form="admin">
        </p>

        <p class="field">
            <input type="hidden" name="notpublishedpass" value="0" form="admin">
            <label for="notpublishedpass">Ask for password</label>
            <input type="checkbox" name="notpublishedpass" id="notpublishedpass" value="1" <?= Wcms\Config::notpublishedpass() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>CSS</h3>

        <p class="field">
            <input type="hidden" name="alertcss" value="0" form="admin">
            <label for="alertcss">Use global.css for those page as well</label>
            <input type="checkbox" name="alertcss" id="alertcss" value="1" <?= Wcms\Config::alertcss() ? 'checked' : '' ?> form="admin">
        </p>

        <p class="info"><i>You can use <code>body.alert</code> class to specify style.</i>
        </p>
    </div>

    <div class="grid-item"  id="render">
        
        <h2>Render</h2>

        <p class="info">To be applied, these modifications need the re-rendering of all pages.
        </p>

        <h3>Rendering details</h3>
        <p class="info">When a page is modified, this may affect the rendering of other pages linked to it.
            The pages to which it points have a strong possibility of needing to be updated too.
            This option will invalidate their rendering each time the page pointing to them is updated.
            They will therefore be re-rendered the next time they are viewed.
        </p>

        <p class="field">
            <input type="hidden" name="deletelinktocache" value="0" form="admin">
            <label for="deletelinktocache">invalidates the rendering of linked pages when updating</label>
            <input type="checkbox" name="deletelinktocache" id="deletelinktocache" value="1" <?= Wcms\Config::deletelinktocache() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>Base page language</h3>

        <p class="info">If the page language is not specified in metadatas, then this default will be used.
        </p>

        <p class="field">
            <label for="lang">Default language</label>
            <input type="text" name="lang" id="lang" value="<?= $this->e(Wcms\Config::lang()) ?>" form="admin" minlength="<?= Wcms\Config::LANG_MIN ?>" maxlength="<?= Wcms\Config::LANG_MAX ?>" required>
        </p>

        <h3>Title</h3>

        <p class="info">This add a suffix to the title of all your pages.
        </p>

        <p class="field">
            <label for="suffix">suffix</label>
            <input type="text" name="suffix" id="suffix" value="<?= $this->e(Wcms\Config::suffix()) ?>" form="admin" maxlength="<?= Wcms\Config::SUFFIX_MAX ?>">
        </p>

        <h3>Links</h3>

        <p class="field">
            <label for="externallinkblank">Open external links in new tab</label>
            <input type="hidden" name="externallinkblank" value="0" form="admin">
            <input type="checkbox" name="externallinkblank" id="externallinkblank" value="1" <?= Wcms\Config::externallinkblank() ? 'checked' : '' ?> form="admin">
        </p>

        <p class="field">
            <input type="hidden" name="internallinkblank" value="0" form="admin">
            <label for="internallinkblank">Open internal links in new tab</label>
            <input type="checkbox" name="internallinkblank" id="internallinkblank" value="1" <?= Wcms\Config::internallinkblank() ? 'checked' : '' ?> form="admin">
        </p>

        <p class="field">
            <input type="hidden" name="urlchecker" value="0" form="admin">
            <label for="urlchecker">Enalbe URL checker</label>
            <input type="checkbox" name="urlchecker" id="urlchecker" value="1" <?= Wcms\Config::urlchecker() ? 'checked' : '' ?> form="admin">
        </p>

        <p class="info">
            URL checker try to reach URLs online to see if they are alives.
            This may cause longer render time if recently activated.
        </p>

        <h3>Images</h3>

        <p class="field">
            <label for="lazyloadimg">Add <em>loading="lazy"</em> attribute to images</label>
            <input type="hidden" name="lazyloadimg" value="0" form="admin">
            <input type="checkbox" name="lazyloadimg" id="lazyloadimg" value="1" <?= Wcms\Config::lazyloadimg() ? 'checked' : '' ?> form="admin">
        </p>

        <p class="field">
            <label for="titlefromalt">Copy <em>alt</em> attribute to <em>title</em> if not set</label>
            <input type="hidden" name="titlefromalt" value="0" form="admin">
            <input type="checkbox" name="titlefromalt" id="titlefromalt" value="1" <?= Wcms\Config::titlefromalt() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>Markdown Parser</h3>

        <p class="field">
            <label for="markdownhardwrap" title="When activated, single line break will be rendered as &lt;br&gt;" >Render soft-linebreaks as &lt;br&gt;</label>
            <input type="hidden" name="markdownhardwrap" value="0" form="admin">
            <input type="checkbox" name="markdownhardwrap" id="markdownhardwrap" value="1" <?= Wcms\Config::markdownhardwrap() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>Url linker</h3>

        <p class="info">This can be overide individualy for each element using render options. See <a href="<?= $this->url('info', [], '#url-linker') ?>">📖 manual section</a> for more infos.
        </p>

        <p class="field">
            <label for="urllinker">transform plain text URLs to links</label>
            <input type="hidden" name="urllinker" value="0" form="admin">
            <input type="checkbox" name="urllinker" id="urllinker" value="1" <?= Wcms\Config::urllinker() ? 'checked' : '' ?> form="admin">
        </p>

        <h3>HTML tags (page V1)</h3>

        <p class="info">This can be overide individualy for each element using render options. See <a href="<?= $this->url('info', [], '#html-tags') ?>">📖 manual section</a> for more infos.
        </p>

        <p class="field">
            <input type="hidden" name="htmltag" value="0" form="admin">
            <label for="htmltag">Print HTML tags around V1 page's contents</label>
            <input type="checkbox" name="htmltag" id="htmltag" value="1" <?= Wcms\Config::htmltag() ? 'checked' : '' ?> form="admin">                        
        </p>
    </div>

    <div class="grid-item"  id="style">

        <h2>Style</h2>

        <h3>Global CSS</h3>

        <p class="field">
            <label for="globalcss">Global CSS will be loaded with every page</label>
            <textarea name="globalcss" id="globalcss" rows="30" spellcheck="false" form="admin"><?= $this->e($globalcss) ?></textarea>
        </p>

        <h3>Favicon</h3>

        <p class="field">
            <label for="defaultfavicon">Default favicon</label>
            <select name="defaultfavicon" id="defaultfavicon" form="admin">
                <option value="">--no favicon--</option>
                <?php foreach ($faviconlist as $favicon) : ?>
                    <option value="<?= $favicon ?>" <?= Wcms\Config::defaultfavicon() === $favicon ? 'selected' : '' ?>><?= $favicon ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <h3>Thumbnail</h3>

        <p class="field">
            <label for="defaultthumbnail">Default thumbnail</label>
            <select name="defaultthumbnail" id="defaultthumbnail" form="admin">
                <option value="">--no thumbnail--</option>
                <?php foreach ($thumbnaillist as $thumbnail) : ?>
                    <option value="<?= $thumbnail ?>" <?= Wcms\Config::defaultthumbnail() === $thumbnail ? 'selected' : '' ?>><?= $thumbnail ?></option>
                <?php endforeach ?>
            </select>
        </p>
    </div>

    <div class="grid-item" id="ldap">
        <h2>LDAP auth</h2>

        <p class="info">
            W authenticates users with a password linked to their account, stored in your instance database.
            If you have an LDAP server, you can choose to authenticate your users with this server instead,
            rather than using W's database to store their password.
            In this case, W will no longer allow user's passwords to be changed.
        </p>

        <h3>LDAP connection infos</h3>

        <p class="info">
            Address of the LDAP server. Should start with:
            <em>ldap://</em> or <em>ldaps://</em>.
            Followed by the server address.
            For a local server, put <em>localhost</em>.
            A port can be specified by adding <em>:port</em> at the end.
        </p>

        <p class="field">
            <label for="ldapserver">LDAP server address</label>
            <input type="text" name="ldapserver" id="ldapserver" value="<?= $this->e(Wcms\Config::ldapserver()) ?>" form="admin" placeholder="ldap://localhost:389">
        </p>

        <p class="info">
            The LDAP tree structure, but without the part containing user identifier.
        </p>

        <p class="field">
            <label for="ldaptree">LDAP hierarchical structure</label>
            <input type="text" name="ldaptree" id="ldaptree" value="<?= $this->e(Wcms\Config::ldaptree()) ?>" form="admin" placeholder="ou=People,dc=domain,dc=tld">
        </p>

        <p class="info">
            The name of the user field in the LDAP database.
        </p>

        <p class="field">
            <label for="ldapu">LDAP user field</label>
            <input type="text" name="ldapu" id="ldapu" value="<?= $this->e(Wcms\Config::ldapu()) ?>" form="admin" placeholder="uid">
        </p>



        <h3>New account creation</h3>

        <p class="info">
            Users can be registered in LDAP but not have an account in W.
            In this case, you can choose to have accounts created by defining the level of these new users.
        </p>

        <p class="field">
            <label for="ldapuserlevel">Level of user that are created.</label>
            <select name="ldapuserlevel" id="ldapuserlevel" form="admin">
                <option value="0">--don't create new users--</option>
                <option value="1"  <?= Wcms\Config::ldapuserlevel() === 1 ? 'selected' : '' ?>>reader</option>
                <option value="2"  <?= Wcms\Config::ldapuserlevel() === 2 ? 'selected' : '' ?>>invite</option>
                <option value="3"  <?= Wcms\Config::ldapuserlevel() === 3 ? 'selected' : '' ?>>editor</option>
                <option value="4"  <?= Wcms\Config::ldapuserlevel() === 4 ? 'selected' : '' ?>>super editor</option>
                <option value="10" <?= Wcms\Config::ldapuserlevel() === 10 ? 'selected' : '' ?>>admin</option>
            </select>
        </p>
    </div>

</main>



<?php if(!Wcms\Config::disablejavascript()) : ?>

<script>
    const taglist = <?= json_encode($taglist) ?>;
</script>

<script type="module" src="<?= Wcms\Model::jspath() ?>admin.bundle.js"></script>

<?php endif ?>


<?php $this->stop('page') ?>
