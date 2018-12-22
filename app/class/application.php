<?php

class Application
{
    public function __construct() {
        
    }

    public function wakeup()
    {
        if(isset($_POST['configinit'])) {
            if(Config::readconfig()) {
                Config::createconfig($_POST['configinit']);
            } else {
                Config::hydrate($_POST['configinit']);
            }
            if(!Config::savejson()) {
                echo 'Cant write config file';
                exit;
            } else{
                header('Location: ./');
                exit;
            }
        } else {
            if(Config::readconfig()) {
                if(!Config::checkbasepath() || empty(Config::admin()) || empty(Config::arttable())) {
                    echo '<ul>';
                    if(!Config::checkbasepath()) {
                        echo '<li>Wrong path</li>';
                    } 
                    if(empty(Config::admin())) {
                        echo '<li>Wrong admin password</li>';
                    } 
                    if(empty(Config::arttable())) {
                        echo '<li>Unset table name</li>';
                    }
                    echo '</ul>';
                    $this->configform();
                    exit;
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
        <div>
        <h2>
        <label for="admin">Admin Password</label>
        </h2>
        <input type="password" name="configinit[admin]" value="<?= Config::admin() ?>" id="admin" minlength="4" maxlength="64">
        <p><i>The main password for administration, you can change it later.</i></p>
        </div>
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
}













?>