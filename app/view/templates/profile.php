<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'profile', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'profile', 'pagelist' => $pagelist]) ?>


<main class="profile">

    <section id="profile">
        <div class="block">

            
            <h1>User : <?= $user->id() ?></h1>
            
            <div class="scroll">


                <h2>Infos</h2>

                <p>Connections count : <?= $user->connectcount() ?></p>

                <p>Account will expire in : <?= $user->expiredate('hrdi') ?></p>



                <h2>Preferences</h2>

                <div id="preferences">

                    <form action="<?= $this->url('profileupdate') ?>" method="post">

                        <input type="number" name="cookie" value="<?= $user->cookie() ?>" id="cookie" min="0" max="<?= Model::MAX_COOKIE_CONSERVATION ?>">
                        <label for="cookie">Cookie conservation time <i>(In days)</i></label>
                        <p>When you tick the <em>remember-me</em> checkbox during login, you can choose how much time <strong>W</strong> will remember you.</p>

                        <input type="submit" value="update preferences">
                        
                    </form>

                    <form action="<?= $this->url('profilepassword') ?>" method="post">
                        <h3>Password</h3>

                        <label for="currentpassword">Actual password</label>
                        </br>
                        <input type="password" name="currentpassword" id="currentpassword" required>
                        </br>

                        <label for="password1">New password</label>
                        </br>
                        <input type="password" name="password1" id="password1" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                        </br>
                        <label for="password2">Confirm new password</label>
                        </br>
                        <input type="password" name="password2" id="password2" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>

                        <p>Password have to be between <?= Wcms\Model::PASSWORD_MIN_LENGTH ?> and <?= Wcms\Model::PASSWORD_MAX_LENGTH ?> characters long.</p>

                        <input type="submit" value="update password">

                    </form>

                </div>

            </div>


        </div>

    </section>

    

</main>

<?php $this->stop('page') ?>