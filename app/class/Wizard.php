<?php

namespace Wcms;

use RuntimeException;

class Wizard
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
     * @throws RuntimeException             in case of error
     */
    public function launch(): void
    {
        try {
            $this->action();
        } catch (RuntimeException $e) {
            throw new RuntimeException('install wizard error: ' . $e->getMessage());
        }
    }

    /**
     * @throws RuntimeException             in case of error
     */
    protected function action(): void
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->handler();
                break;

            case 'GET':
                $this->form(!$this->usermanager->adminexist());
                break;

            default:
                throw new RuntimeException('bad request method');
        }
    }

    /**
     * @throws RuntimeException if default bookmark creation failed
     */
    protected function handler(): void
    {

        if (isset($_POST['configinit'])) {
            Config::hydrate($_POST['configinit']);
            Config::getdomain();
            Config::savejson();

            if (isset($_POST['userinit'])) {
                $user = new User($_POST['userinit']);
                $user->setlevel(User::ADMIN);
                $user->hashpassword();
                $this->usermanager->add($user);
            }

            if (boolval($_POST['defaultbookmarks'])) {
                try {
                    $bookmarkmanager = new Modelbookmark();
                    $bookmarkmanager->defaults();
                } catch (RuntimeException $e) {
                    // Just log a warning as the wizard will be skipped on reload and it's not a big problem
                    Logger::warning('install wizard: default bookmarks creation: %s', $e->getMessage());
                }
            }

            header('Location: ./');
            exit;
        }
    }

    protected function form(bool $adminform): void
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
            <input
                type="text"
                name="configinit[pagetable]" 
                value="<?= empty(Config::pagetable()) ? 'mystore' : Config::pagetable() ?>"
                id="pagetable"
            >
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
}













?>
