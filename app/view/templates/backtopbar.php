<header class="hbar" id="topbar">

    <div class="hbar-section">
        <form action="<?= $this->url('search') ?>" method="post"  id="search">
            <input type="text" list="searchdatalist" name="id" id="search" placeholder="page id" required <?= $tab !== 'edit' && $tab !== null ? 'autofocus' : '' ?>>
            <input type="submit" name="action" value="read">
            <?= $user->iseditor() ? '<input type="submit" name="action" value="edit">' : '' ?>
            <?php if($user->iseditor()) : ?>
                <datalist id="searchdatalist">
                    <?php foreach ($pagelist as $id) : ?>
                        <option value="<?= $id ?>"><?= $id ?></option>
                    <?php endforeach ?>
                </datalist>
            <?php endif ?>
        </form>

        <?php if($user->iseditor()) : ?>                
            <a href="<?= $this->url('home') ?>" <?= $tab == 'home' ? 'class="currentpage"' : '' ?>>
                <i class="fa fa-home"></i> <span>home</span>
            </a>
            <a href="<?= $this->url('media') ?>" <?= $tab == 'media' ? 'class="currentpage"' : '' ?>>
                <i class="fa fa-link"></i> <span>media</span>
            </a>
        <?php endif ?>

        <?php if (Wcms\Config::homepage() === 'redirect' && !empty(Wcms\Config::homeredirect())) : ?>
            <a href="<?= $this->url('pageread', ['page' => Wcms\Config::homeredirect()]) ?>" title="open landing page">
                <i class="fa fa-external-link-square"></i>
                <span><?= Wcms\Config::homeredirect() ?></span>
            </a>
        <?php endif ?>
    </div>

    <div id="user" class="hbar-section">
        <?php if($user->isadmin()) : ?>
            <a href="<?= $this->url('user') ?>" <?= $tab == 'user' ? 'class="currentpage"' : '' ?>>
                <i class="fa fa-users"></i> <span>users</span>
            </a>
            <a href="<?= $this->url('admin') ?>" <?= $tab == 'admin' ? 'class="currentpage"' : '' ?>>
                <i class="fa fa-cog"></i> <span>admin</span>
            </a>
        <?php endif ?>

        <a href="<?= $this->url('info') ?>"  <?= $tab == 'info' ? 'class="currentpage"' : '' ?>>
            <i class="fa fa-book"></i> <span>documentation</span>
        </a>

        <a href="<?= $this->url('profile') ?>" title="Edit my profile" <?= $tab == 'profile' ? 'class="currentpage"' : '' ?> >
            <i class="fa fa-user"></i> <span><?= $user->id() ?></span>
        </a>

        <form action="<?= $this->url('log') ?>" method="post" id="connect">
            <input type="submit" name="log" value="logout" >
            <?php if($tab === 'edit') : ?>
                <input type="hidden" name="route" value="pageread">
                <input type="hidden" name="id" value="<?= $pageid ?>">
            <?php endif ?>
        </form>
    </div>

</header>
