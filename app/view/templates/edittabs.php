<div class="tabs" data-markdownheading="<?= $workspace->markdownheading() ?>">

    <?php foreach ($page->tabs() as $key => $value) : ?>
    <div class="tab">

        <input form="update" name="interface" type="radio" value="<?= $key ?>" id="tab<?= $key ?>" class="checkboxtab" <?= $key == $page->interface() ? 'checked' : '' ?> >

        <label for="tab<?= $key ?>" <?= empty($templates[$key]) ? '' : 'title="template : '.$templates[$key].'" ' ?> class="<?= empty($templates[$key]) ? '' : 'template' ?> <?= empty($value) ? '' : 'edited' ?>"><?= $key ?> </label>

        <div class="content">

            <textarea
                name="<?= $key ?>"
                class="editorarea"
                id="edit<?= $key ?>"
                autocapitalize="off"
                form="update"
                <?php if (!in_array($key, \Wcms\Page::TABS)) : ?>
                    spellcheck="true"
                    lang="<?= empty($page->lang()) ? Wcms\Config::lang() : $page->lang() ?>"
                <?php endif ?>
                <?= $key == $page->interface() ? 'autofocus' : '' ?>
            ><?= $this->e($value) ?></textarea>
        </div>
    </div>
    <?php endforeach ?>

</div>
