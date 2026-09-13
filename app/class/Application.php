<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Databaseexception;

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

    /**
     * @throws RuntimeException if default bookmark creation failed
     */
    public function wakeup(): void
    {
        if (isset($_POST['configinit'])) {
                Config::hydrate($_POST['configinit']);
            Config::getdomain();
            if (boolval($_POST['defaultbookmarks'])) {
                $this->defaultbookmarks();
            }
            Config::savejson();

            if (isset($_POST['userinit'])) {
                $user = new User($_POST['userinit']);
                $user->setlevel(User::ADMIN);
                $user->hashpassword();
                $this->usermanager->add($user);
            }
            header('Location: ./');
            exit;
        } else {
            $this->configform(!$this->usermanager->adminexist());
        }
    }

    protected function configform(bool $adminform): void
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
                <label for="pagetable">Name of your page database</label>
            </h2>
            <input type="text" name="configinit[pagetable]"  value="<?= Config::pagetable() ?>" id="pagetable">
            <p><i>Set the name of the folder that is going to store the pages</i></p>
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
                (<?= Config::SECRET_KEY_MIN ?> to <?= Config::SECRET_KEY_MAX ?> characters)
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
        <?php if ($adminform) {
            $this->adminform();
        } ?>
        <input type="submit" value="set">
        </form>

        <?php
    }

    protected function adminform(): void
    {
        ?>
        <div>
        <h2>
        <label for="id">Your identifier</label>
        </h2>
        <input type="text" name="userinit[id]" id="admin" maxlength="<?= Model::MAX_ID_LENGTH ?>" required>
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

        <?php
    }

    /**
     * Create default bookmarks set during install
     *
     * @throws Databaseexception
     */
    protected function defaultbookmarks(): void
    {
        $bookmarkmanager = new Modelbookmark();
        if (empty($bookmarkmanager->list())) {
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
        }
    }
}













?>
