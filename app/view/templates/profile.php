<?php
    use Wcms\Model;
    $this->layout('layout', ['title' => 'profile', 'stylesheets' => [$css . 'back.css', $css . 'profile.css']]) 
?>

<?php $this->start('page') ?>
<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'profile', 'pagelist' => $pagelist]) ?>

<main class="profile grid">

    <div class="grid-item" id="profile">
            
        <h2><span><i class="fa fa-user"></i> User profile</span></h2>
        <table>
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
                    <td>account expiration</td>
                    <td><?= $user->expiredate('hrdi') ?></td>
                </tr>
            </tbody>
        </table>

    </div>

    <div class="grid-item" id="preferences">

        <h2>Preferences</h2>

        <form action="<?= $this->url('profileupdate') ?>" method="post" id="preferences">

            <p class="info">Change some infos about you.</p>

            <p class="field">
                <label for="name">Display name (if none, user ID will be used)</label>
                <input type="text" name="name" id="name" value="<?= $this->e($user->name()) ?>" placeholder="<?= $user->id() ?> " maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>">
            </p>
            
            <p class="field">
                <label for="url">Associated url (can be a page ID)</label>
                <input type="text" name="url" id="url" value="<?= $this->e($user->url()) ?>" list="searchdatalist" placeholder="URL or page ID" maxlength="<?= Wcms\Item::LENGTH_SHORT_TEXT ?>" >
            </p>
            
            <p class="info">When you tick the <em>remember-me</em> checkbox during login, you can choose how much time <strong>W</strong> will remember you.</p>

            <p class="field">
                <label for="cookie">Cookie conservation time <i>(In days)</i></label>
                <input type="number" name="cookie" value="<?= $user->cookie() ?>" id="cookie" min="0" max="<?= Model::MAX_COOKIE_CONSERVATION ?>" >
            </p>
            
            <p class="field submit-field">
                <input type="submit" value="update preferences">
            </p>
            
        </form>
    </div>

    <?php if (!$user->isldap()) : ?>
        <div class="grid-item" id="password">
            <h2>Password</h2>

            <form action="<?= $this->url('profilepassword') ?>" method="post" id="password">

                <p class="info">Password have to be between <?= Wcms\Model::PASSWORD_MIN_LENGTH ?> and <?= Wcms\Model::PASSWORD_MAX_LENGTH ?> characters long.</p>

                <p class="field">
                    <label for="currentpassword">Actual password</label>
                    <input type="password" name="currentpassword" id="currentpassword" required>
                </p>

                <p class="field">
                    <label for="password1">New password</label>
                    <input type="password" name="password1" id="password1" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                </p>

                <p class="field">
                    <label for="password2">Confirm new password</label>
                    <input type="password" name="password2" id="password2" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" required>
                </p>

                <p class="field">
                    <input type="submit" value="update password">
                </p>

            </form>

        </div>
    <?php endif ?>

</main>

<?php $this->stop('page') ?>
