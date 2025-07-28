<?php
    use Wcms\Model;
    $this->layout('backlayout', ['title' => 'profile', 'stylesheets' => [$css . 'back.css', $css . 'profile.css'], 'theme' => $theme]) 
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

            <p class="info">
                Don't like the interface theme set by administrator&nbsp;? Overide it with your preference.
            </p>

            <p class="field">
                <label for="theme">Personnal interface theme</label>
                <select name="theme" id="theme">
                    <option value="">--no personnal theme--</option>
                    <?php foreach ($themes as $theme) : ?>
                        <option value="<?= $theme ?>" <?= $theme === $user->theme() ? 'selected' : '' ?>><?= $theme ?></option>
                    <?php endforeach ?>
                </select>
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

    <div class="grid-item" id="sessions">
        <h2>Sessions</h2>

        <p class="info">
            Sessions are created when you tick the "remember me" checkbox during login.
        </p>

        <ul>
            <?php foreach ($user->sessions() as $hash => $name) : ?>
                <li>
                    <h4>
                        <?= $name ?>
                    </h4>
                </li>
            <?php endforeach ?>
        </ul>

        <p class="info">
            Deleting all sessions will disconnect you from every places you've checked "remember me".
        </p>

        <p class="field submit-field">
            <a href="<?= $this->url('profiledeletesessions') ?>" class="button">
                <i class="fa fa-trash"></i>
                delete all sessions
            </a>
        </p>
    </div>

</main>

<?php $this->stop('page') ?>
