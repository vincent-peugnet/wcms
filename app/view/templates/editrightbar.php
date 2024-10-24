<aside id="rightbar" class="toggle-panel-container">
    <input id="showeditorrightpanel" name="showeditorrightpanel" value="1" class="toggle-panel-toggle" type="checkbox" <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?> form="workspace-form" >
    <label for="showeditorrightpanel" class="toggle-panel-label"><span><i class="fa fa-info"></i></span></label>

    <div class="toggle-panel" id="rightbarpanel">

        <h2>Infos</h2>
        
        <?php if ($user->iseditor()) : ?>
            <h4>Authors</h4>

            <?php foreach ($editorlist as $editor) : ?>
                <div class="checkexternal">
                <input
                    type="checkbox"
                    name="authors[]"
                    id="<?= $editor->id() ?>"
                    value="<?= $editor->id() ?>"
                    form="update"
                    <?= in_array($editor->id(), $page->authors()) ? 'checked' : '' ?>

                    <?php /* safeguard against editor removing themself from authors too easily */ ?>
                    <?= !$user->issupereditor() && $editor->id() === $user->id() ? 'disabled=""' : '' ?>
                >
                <label for="<?= $editor->id() ?>" ><?= $editor->id() ?> <?= $editor->level() ?></label>
                </div>
            <?php endforeach ?>

        <?php endif ?>



        <h4>Stats</h4>

        <table>
            <tbody>
                <tr>
                    <td>edition:</td>
                    <td><?= $page->editcount() ?></td>
                </tr>
                <tr>
                    <td>display:</td>
                    <td><?= $page->displaycount() ?></td>
                </tr>
                <tr>
                    <td>visit:</td>
                    <td><?= $page->visitcount() ?></td>
                </tr>
            </tbody>
        </table>

    </div>

</aside>
