<?php if(empty($selection)) : ?>
<?php foreach ($others as $template) : ?>
    <option value="<?= $template ?>" <?= $selected === $template ? 'selected' : '' ?>><?= $template ?></option>
<?php endforeach ?>
<?php else : ?>
<optgroup label="selection">
    <?php foreach ($selection as $template) : ?>
        <option value="<?= $template ?>" <?= $selected === $template ? 'selected' : '' ?>><?= $template ?></option>
    <?php endforeach ?>
</optgroup>
<optgroup label="others">
    <?php foreach ($others as $template) : ?>
        <option value="<?= $template ?>" <?= $selected === $template ? 'selected' : '' ?>><?= $template ?></option>
    <?php endforeach ?>
</optgroup>
<?php endif ?>
