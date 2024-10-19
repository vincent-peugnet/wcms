<div id="rightbar" class="bar">
    <input
        id="showeditorrightpanel"
        name="showeditorrightpanel"
        value="1"
        class="toggle"
        type="checkbox"
        form="workspace-form"
        <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?>
    >
    <label for="showeditorrightpanel" class="toogle">◧</label>
    <div id="rightbarpanel" class="panel">
   
        
        <?php if ($user->iseditor()) : ?>
            <h3>Authors</h3>

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



        <h3>Stats</h3>

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

</div>
