<div class="tabs">

<?php foreach ($tablist as $key => $value) { ?>
<div class="tab">

    <input name="interface" type="radio" value="<?= $key ?>" id="tab<?= $key ?>" class="checkboxtab" <?= $key == $opentab ? 'checked' : '' ?> >

    <label for="tab<?= $key ?>" <?= empty($templates[$key]) ? '' : 'title="template : '.$templates[$key].'" ' ?> class="<?= empty($templates[$key]) ? '' : 'template' ?> <?= empty($value) ? '' : 'edited' ?>"><?= $key ?> </label>

    <div class="content">

        <textarea name="<?= $key ?>"
                  id="edit<?= $key ?>"
                  autocomplete="off"
                  autocorrect="off"
                  autocapitalize="off"
                  spellcheck="false"
                  <?= $key == $opentab ? 'autofocus' : '' ?>
        ><?= $this->e($value) ?></textarea>
    </div>
</div>
<?php } ?>

</div>