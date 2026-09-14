<?php

namespace Wcms;

use RuntimeException;
use Wcms\Exception\Filesystemexception;

class Wizard
{
    /**
     * @var Modeluser
     */
    protected $usermanager;

    public const LOCK_FILE = 'config.wizard.lock';

    public function __construct()
    {
        $this->usermanager = new Modeluser();
    }

    /**
     * @throws RuntimeException             in case of error
     */
    public function launch(): void
    {
        if (self::islocked()) {
            throw new RuntimeException(
                "missing config file, install wizard locked: delete 'config.wizard.lock' for access"
            );
        }

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
        $msgs = [];

        // admin user creation
        if (isset($_POST['userinit'])) {
            $user = new User($_POST['userinit']);
            $user->setlevel(User::ADMIN);
            $user->hashpassword();
            $this->usermanager->add($user);
            $msgs[] = 'user created';
        }

        Config::hydrate($_POST['configinit']);
        Config::getdomain();

        $errors = Config::check();
        if (!empty($errors)) {
            echo '<a href="">⬅️ back to form</a>';
            foreach ($errors as $error) {
                echo "<p>❌ $error</p>";
            }
            exit;
        }

        Config::savejson();
        self::lock(); // lock the wizard

        // default pages
        if (boolval($_POST['defaultpages'])) {
            try {
                $pagemanager = new Modelpage(Config::pagetable());
                $pagemanager->usedefaults();
            } catch (RuntimeException $e) {
                // Just log a warning as the wizard will be skipped on reload and it's not a big problem
                Logger::warning('install wizard: default pages creation: %s', $e->getMessage());
            }
        }

        // default bookmarks
        if (boolval($_POST['defaultbookmarks'])) {
            try {
                $bookmarkmanager = new Modelbookmark();
                $bookmarkmanager->defaults();
                $msgs[] = 'default bookmarks created';
            } catch (RuntimeException $e) {
                // Just log a warning as the wizard will be skipped on reload and it's not a big problem
                Logger::warning('install wizard: default bookmarks creation: %s', $e->getMessage());
            }
        }

        header('Location: ./');
    }

    /**
     * @throws Filesystemexception          if an error occured
     */
    public static function lock(): void
    {
        Fs::writefile(self::LOCK_FILE, 'delete this file to enable setup wizard');
    }

    /**
     * Indicate if the wizard is locked based on the existence of the lock file
     */
    public static function islocked(): bool
    {
        return file_exists(self::LOCK_FILE);
    }

    protected function form(bool $adminform): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="color-scheme" content="light dark" />
            <title>🪄 W install wizard</title>
        </head>
        <body>
            <h1>🪄 W install wizard</h1>
            <p><code>version: <?= getversion() ?></code></p>
            <form action="" method="post">
                <?php if ($adminform) {
                    $this->adminform();
                } ?>
                <?php $this->configform() ?>
                <p>
                    <input type="submit" value="submit">
                </p>
            </form>
        </body>
        <style>
            body {
                max-width: 900px;
                margin: auto;
                padding: 8px;
            }
            input[type="submit"] {padding: 5px 20px;cursor: pointer;}
            h3 {margin-bottom: 0;font-size: 1em;}
            legend {font-size: 1.3em;font-weight: bold;}
            .help {font-size: 0.9em;}
        </style>
        </html>


        <?php
    }

    protected function configform(): void
    {
        ?>
        <fieldset>
            <legend>Config file</legend>
            <input type="hidden" name="secure" value="0">
            <input type="checkbox" name="secure" id="secure" value="1" <?= Config::issecure() ? "checked" : "" ?>>
            <label for="secure">secure connection (<code>https</code>)</label>
            <h3>
                <label for="basepath">Path to W-CMS</label>
            </h3>
            <input type="text" name="configinit[basepath]"  value="<?= Config::basepath() ?>" id="basepath">
            <p class="help">
                Leave it empty if W-CMS is in your root folder, otherwise,
                indicate the subfolder(s) in witch you installed it
            </p>
            <h3>
                <label for="pagetable">Name of the pages database</label>
            </h3>
            <input
                type="text"
                name="configinit[pagetable]" 
                value="<?= empty(Config::pagetable()) ? 'mystore' : Config::pagetable() ?>"
                id="pagetable"
                required
            >
            <p class="help">Set the name of the folder that is going to store the pages</p>
            <h3>
                <label for="secretkey">Secret key</label>
            </h3>
            <input
                type="text"
                name="configinit[secretkey]"
                value="<?= bin2hex(randombytes(10)) ?>"
                id="secretkey"
                minlength="<?= Config::SECRET_KEY_MIN ?>"
                maxlength="<?= Config::SECRET_KEY_MAX ?>"
                required
            >
            <p class="help">
                The secret key is used to secure cookies. There are no need to remind it.
                (<?= Config::SECRET_KEY_MIN ?> to <?= Config::SECRET_KEY_MAX ?> characters)
            </p>
            <input type="hidden" name="defaultbookmarks" value="0">
            <input type="checkbox" name="defaultbookmarks" id="defaultbookmarks" value="1" checked>
            <label for="defaultbookmarks">default bookmarks</label>
            <p class="help">
                Gives you a set of defaults. Usefull in most case 😉
            </p>
            <input type="hidden" name="defaultpages" value="0">
            <input type="checkbox" name="defaultpages" id="defaultpages" value="1" checked>
            <label for="defaultpages">default pages</label>
            <p class="help">
                Usefull if it's your first time using W 🍼
            </p>
        </fieldset>
        <?php
    }

    protected function adminform(): void
    {
        ?>
        <fieldset>
            <legend>Admin user</legend>
            <p class="help">Your credentials as the first administrator</p>
            <h3>
            <label for="admin">Identifier</label>
            </h3>
            <input type="text" name="userinit[id]" id="admin" maxlength="<?= Model::MAX_ID_LENGTH ?>" required>
            </div>
            <div>
            <h3>
            <label for="password">Password</label>
            </h3>
            <input
                type="password"
                name="userinit[password]"
                id="password"
                minlength="<?= Model::PASSWORD_MIN_LENGTH ?>"
                maxlength="<?= Model::PASSWORD_MAX_LENGTH ?>"
                required
            >
        </fieldset>

        <?php
    }
}













?>
