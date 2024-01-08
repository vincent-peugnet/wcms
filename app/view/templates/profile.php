<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'profile', 'stylesheets' => [$css . 'back.css', $css . 'profile.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'profile', 'pagelist' => $pagelist]) ?>


<main class="profile">

    <section id="profile">
        <div class="block">

            
            <h1><i class="fa fa-user"></i> User profile</h1>
            
            <div class="scroll">
                
                <table>
                    <thead>
                        <tr>
                            <th>stat</th>
                            <th>value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>id</td>
                            <td><?= $user->id() ?></td>
                        </tr>
                        <tr>
                            <td>connection counter</td>
                            <td><?= $user->connectcount() ?></td>
                        </tr>
                        <tr>
                            <td>account expirations</td>
                            <td><?= $user->expiredate('hrdi') ?></td>
                        </tr>
                    </tbody>
                </table>

                <h2>Preferences</h2>


                <form action="<?= $this->url('profileupdate') ?>" method="post" id="preferences">

                    <p>Change some infos about you.</p>

                    <label for="name">Display name (if none, user ID will be used)</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="<?= $user->name() ?>"
                        placeholder="<?= $user->id() ?> "
                        maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>"
                    >
                    </br>


                    <label for="url">associated url (can be a page ID)</label>
                    <input
                        type="text"
                        name="url"
                        id="url"
                        value="<?= $user->url() ?>"
                        list="searchdatalist"
                        placeholder="URL or page ID"
                        maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>"
                    >
                    </br>

                    <p>When you tick the <em>remember-me</em> checkbox during login, you can choose how much time <strong>W</strong> will remember you.</p>
                    <input
                        type="number"
                        name="cookie"
                        value="<?= $user->cookie() ?>"
                        id="cookie"
                        min="0"
                        max="<?= Model::MAX_COOKIE_CONSERVATION ?>"
                    >
                    <label for="cookie">Cookie conservation time <i>(In days)</i></label>
                    <input type="submit" value="update preferences">
                    
                </form>

                <h2>Password</h2>


                <form action="<?= $this->url('profilepassword') ?>" method="post" id="password">

                    <p>Password have to be between <?= Wcms\Model::PASSWORD_MIN_LENGTH ?> and <?= Wcms\Model::PASSWORD_MAX_LENGTH ?> characters long.</p>

                    <label for="currentpassword">Actual password</label>
                    <input type="password" name="currentpassword" id="currentpassword" required>

                    <label for="password1">New password</label>
                    <input type="password" name="password1" id="password1" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                    <label for="password2">Confirm new password</label>
                    <input type="password" name="password2" id="password2" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                    <input type="submit" value="update password">



                </form>

            </div>


        </div>

    </section>

    

</main>

<?php $this->stop('page') ?>
