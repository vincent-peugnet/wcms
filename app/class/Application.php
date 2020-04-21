<?php

namespace Wcms;

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
            if (!Config::savejson()) {
                echo 'Cant write config file';
                exit;
            } else {
                header('Location: ./');
                exit;
            }
        } elseif (
            isset($_POST['userinit'])
            && !empty($_POST['userinit']['id'])
            && !empty($_POST['userinit']['password'])
        ) {
            $userdata = $_POST['userinit'];
            $userdata['level'] = 10;
            $user = new User($userdata);
            $this->usermanager->add($user);
            header('Location: ./');
            exit;
        } else {
            if (Config::readconfig()) {
                if (
                    !Config::checkbasepath()
                    || empty(Config::pagetable())
                    || !is_dir(Model::RENDER_DIR)
                    || !Config::checkdomain()
                    || empty(Config::secretkey())
                ) {
                    echo '<ul>';
                    if (!Config::checkbasepath()) {
                        echo '<li>Wrong path</li>';
                    }
                    if (empty(Config::pagetable())) {
                        echo '<li>Unset table name</li>';
                    }
                    if (!Config::checkdomain()) {
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

    public function configform()
    {
        ?>
        <h1>Configuration</h1>

        <h3>Version :</h3>

        <p><?= getversion() ?></p>
        
        <form action="" method="post">
        <div>
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
                value="<?= bin2hex(random_bytes(10)) ?>"
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
        <input type="submit" value="set">
        </form>

        <?php
    }

    public function adminform()
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
}













?>