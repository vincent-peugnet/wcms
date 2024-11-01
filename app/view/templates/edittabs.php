<div class="tabs">

    <?php foreach ($tablist as $key => $value) : ?>
    <div class="tab">

        <input form="update" name="interface" type="radio" value="<?= $key ?>" id="tab<?= $key ?>" class="checkboxtab" <?= $key == $opentab ? 'checked' : '' ?> >

        <label for="tab<?= $key ?>" <?= empty($templates[$key]) ? '' : 'title="template : '.$templates[$key].'" ' ?> class="<?= empty($templates[$key]) ? '' : 'template' ?> <?= empty($value) ? '' : 'edited' ?>"><?= $key ?> </label>

        <div class="content">

            <textarea name="<?= $key ?>"
                    class="editorarea"
                    id="edit<?= $key ?>"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
                    form="update"
                    <?= $key == $opentab ? 'autofocus' : '' ?>
            ><?= $this->e($value) ?></textarea>
        </div>
    </div>
    <?php endforeach ?>

</div>
