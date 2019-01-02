<?php

class Application
{
    /**
     * @var Modeluser
     */
    protected $usermanager;

    public function __construct() {
        $this->usermanager = new Modeluser();
    }

    public function wakeup()
    {
        if(isset($_POST['configinit'])) {
            if(Config::readconfig()) {
                Config::createconfig($_POST['configinit']);
            } else {
                Config::hydrate($_POST['configinit']);
            }
            if(!is_dir(Model::RENDER_DIR)) {
                mkdir(Model::RENDER_DIR);
            }
            if(!Config::savejson()) {
                echo 'Cant write config file';
                exit;
            } else{
                header('Location: ./');
                exit;
            }
        } elseif(isset($_POST['userinit'])) {
            $userdata = $_POST['userinit'];
            $userdata['level'] = 10;
            //$userdata['id'] = idclean($userdata['id']);
            $user = new User($userdata);
            $this->usermanager->add($user);
            header('Location: ./');
            exit;

        } else {
            if(Config::readconfig()) {
                if(!Config::checkbasepath() || empty(Config::arttable()) || !is_dir(Model::RENDER_DIR)) {
                    echo '<ul>';
                    if(!Config::checkbasepath()) {
                        echo '<li>Wrong path</li>';
                    }
                    if(empty(Config::arttable())) {
                        echo '<li>Unset table name</li>';
                    }
                    if(!is_dir(Model::RENDER_DIR)) {
                        echo '<li>Render path not existing</li>';
                    }
                    echo '</ul>';
                    $this->configform();
                    exit;
                } else {
                    if($this->usermanager->admincount() === 0) {
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
        <p><i>Leave it empty if W-CMS is in your root folder, otherwise, indicate the subfolder(s) in witch you installed the CMS</i></p>
        </div>
        <div>
        <h2>
        <label for="arttable">Name of your database table</label>
        </h2>
        <input type="text" name="configinit[arttable]"  value="<?= Config::arttable() ?>" id="arttable">
        <p><i>Set the name of the first folder that is going to store all your work</i></p>
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
        <input type="text" name="userinit[id]" value="<?= Config::admin() ?>" id="admin" maxlength="64">
        <p><i>Your user id as the first administrator.</i></p>
        </div>
        <div>
        <h2>
        <label for="password">Your password</label>
        </h2>
        <input type="password" name="userinit[password]" value="<?= Config::admin() ?>" id="admin" minlength="4" maxlength="64">
        <p><i>Your user passworder as first administrator.</i></p>
        </div>
        <input type="submit" value="set">
        </form>

        <?php
    }
}













?>