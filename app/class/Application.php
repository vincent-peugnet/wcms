<?php

namespace Wcms;

use RuntimeException;

class Application
{
    /**
     * @var Modeluser
     */
    protected $usermanager;

    public function __construct()
    {
        $this->usermanager = new Modeluser();
    }

    public function wakeup()
    {
        if (isset($_POST['configinit'])) {
            if (Config::readconfig()) {
                Config::createconfig($_POST['configinit']);
            } else {
                Config::hydrate($_POST['configinit']);
            }
            Config::getdomain();
            if (!is_dir(Model::RENDER_DIR)) {
                mkdir(Model::RENDER_DIR);
            }
            if (boolval($_POST['defaultbookmarks'])) {
                $this->defaultbookmarks();
            }
            try {
                Config::savejson();
            } catch (RuntimeException $e) {
                echo 'Cant write config file : ' . $e->getMessage();
            }
            header('Location: ./');
        } elseif (
            isset($_POST['userinit'])
            && !empty($_POST['userinit']['id'])
            && !empty($_POST['userinit']['password'])
        ) {
            $userdata = $_POST['userinit'];
            $userdata['level'] = 10;
            $user = new User($userdata);
            $user->hashpassword();
            $this->usermanager->add($user);
            header('Location: ./');
            exit;
        } else {
            if (Config::readconfig()) {
                if (
                    !Config::checkbasepath()
                    || empty(Config::pagetable())
                    || !is_dir(Model::RENDER_DIR)
                    || empty(Config::domain())
                    || empty(Config::secretkey())
                ) {
                    echo '<ul>';
                    if (!Config::checkbasepath()) {
                        echo '<li>Wrong path</li>';
                    }
                    if (empty(Config::pagetable())) {
                        echo '<li>Unset table name</li>';
                    }
                    if (empty(Config::domain())) {
                        echo '<li>Need to recheck the domain</li>';
                    }
                    if (!is_dir(Model::RENDER_DIR)) {
                        echo '<li>Render path not existing</li>';
                    }
                    if (!is_dir(Model::RENDER_DIR)) {
                        echo '<li>Secret Key not set or not valid</li>';
                    }
                    echo '</ul>';
                    $this->configform();
                    exit;
                } else {
                    if ($this->usermanager->admincount() === 0) {
                        echo 'missing admin user';
                        $this->adminform();
                        exit;
                    }
                }
            } else {
                echo 'Missing config file';
                $this->configform();
                exit;
            }
        }
    }

    protected function configform()
    {
        ?>
        <h1>Configuration</h1>

        <h3>Version :</h3>

        <p><?= getversion() ?></p>
        
        <form action="" method="post">
        <div>
            <h2>
                Secure connection
            </h2>
            <input type="hidden" name="secure" value="0">
            <input type="checkbox" name="secure" id="secure" value="1" <?= Config::issecure() ? "checked" : "" ?>>
            <label for="secure">secure connection</label>
            <p>
                Should be checked if your web server is using HTTPS
            </p>
            <h2>
                <label for="basepath">Path to W-CMS</label>
            </h2>
            <input type="text" name="configinit[basepath]"  value="<?= Config::basepath() ?>" id="basepath">
            <p><i>
                Leave it empty if W-CMS is in your root folder, otherwise,
                indicate the subfolder(s) in witch you installed the CMS
            </i></p>
        </div>
        <div>
            <h2>
                Page version
            </h2>
            <p>
                Select the page version you want to use. If you don't know what it means, keep version 2.
            </p>
            <select name="configinit[pageversion]" id="pageversion">
                <option value="1">v1</option>
                <option value="2" selected>v2</option>
            </select>
        </div>
        <div>
            <h2>
                <label for="pagetable">Name of your database table</label>
            </h2>
            <input type="text" name="configinit[pagetable]"  value="<?= Config::pagetable() ?>" id="pagetable">
            <p><i>Set the name of the first folder that is going to store all your work</i></p>
        </div>
        <div>
            <h2>
                <label for="secretkey">Secret Key</label>
            </h2>
            <input
                type="text"
                name="configinit[secretkey]"
                value="<?= bin2hex(randombytes(10)) ?>"
                id="secretkey"
                minlength="<?= Config::SECRET_KEY_MIN ?>"
                maxlength="<?= Config::SECRET_KEY_MAX ?>"
                required
            >
            <p><i>
                The secret key is used to secure cookies. There are no need to remind it.
                (16 to 128 characters)
            </i></p>
        </div>
        <div>
            <h2>default</h2>
            <input type="hidden" name="defaultbookmarks" value="0">
            <input type="checkbox" name="defaultbookmarks" id="defaultbookmarks" value="1" checked>
            <label for="defaultbookmarks">default bookmarks</label>
            <p>
                Gives you a set of default bookmarks. Usefull in most case 😉.
            </p>
        </div>
        <input type="submit" value="set">
        </form>

        <?php
    }

    protected function adminform()
    {
        ?>

        <form action="" method="post">
        <div>
        <h2>
        <label for="id">Your identifiant</label>
        </h2>
        <input type="text" name="userinit[id]" id="admin" maxlength="64" required>
        <p><i>Your user id as the first administrator.</i></p>
        </div>
        <div>
        <h2>
        <label for="password">Your password</label>
        </h2>
        <input
            type="password"
            name="userinit[password]"
            id="password"
            minlength="<?= Model::PASSWORD_MIN_LENGTH ?>"
            maxlength="<?= Model::PASSWORD_MAX_LENGTH ?>"
            required
        >
        <p><i>Your user passworder as first administrator.</i></p>
        </div>
        <input type="submit" value="set">
        </form>

        <?php
    }

    /**
     * Create default bookmarks set during install
     */
    protected function defaultbookmarks()
    {
        $bookmarkmanager = new Modelbookmark();
        if (empty($bookmarkmanager->list())) {
            try {
                $lastedited = new Opt(['sortby' => 'datemodif', 'limit' => 5, 'order' => -1]);
                $lasteditedbookmark = new Bookmark();
                $lasteditedbookmark->init(
                    'last5edited',
                    $lastedited->getaddress(),
                    '🕒',
                    'Last 5 edited',
                    'Get the 5 last edited pages of the database'
                );
                $lastcreated = new Opt(['sortby' => 'datecreation', 'limit' => 10, 'order' => -1]);
                $lastcreatedbookmark = new Bookmark();
                $lastcreatedbookmark->init(
                    'last10created',
                    $lastcreated->getaddress(),
                    '🖍️',
                    'Last 10 created',
                    'Get the 10 last created pages of the database'
                );
                $emptytag = new Opt(['tagcompare' => 'EMPTY']);
                $emptytagbookmark = new Bookmark();
                $emptytagbookmark->init(
                    'notags',
                    $emptytag->getaddress(),
                    '🏷️',
                    'No tags',
                    'Pages that does\'nt have any tag'
                );
                $all = new Opt();
                $allbookmark = new Bookmark();
                $allbookmark->init('all', $all->getaddress(), '⚓', 'All', 'Show all pages');
                    $bookmarkmanager->add($lasteditedbookmark);
                    $bookmarkmanager->add($lastcreatedbookmark);
                    $bookmarkmanager->add($emptytagbookmark);
                    $bookmarkmanager->add($allbookmark);
            } catch (RuntimeException $e) {
            }
        }
    }
}













?>
