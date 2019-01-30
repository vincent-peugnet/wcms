<div id="rightbar" class="bar">
    <input id="showrightpanel" name="workspace[showrightpanel]" value="1" class="toggle" type="checkbox"  <?= $showrightpanel == true ? 'checked' : '' ?>>
    <label for="showrightpanel" class="toogle">◧</label>
    <div id="rightbarpanel" class="panel">
    

    <details id="lastedited" open>
        <summary>Last edited</summary>
    <ul>
    <?php
    foreach ($lasteditedartlist as $id) {
        ?>
        <li><a href="<?= $this->uart('artedit', $id) ?>"><?= $id === $art->id() ? '➤' : '✎' ?> <?= $id ?></a></li>
        <?php
    }

    ?>
    </ul>

    </details>


    <details id="tags" open>
        <summary>Tags</summary>
        <?php
        foreach ($tagartlist as $tag => $idlist) {
            if(count($idlist) > 1) {
            ?>
            <strong><?= $tag ?></strong>
            <?php

            echo '<ul>';
            foreach ($idlist as $id) {
                if($id === $art->id()) {
                    echo '<li>➤ '.$id.'</li>';
                } else {
                ?>
                <li><a href="<?= $this->uart('artedit', $id) ?>">✎ <?= $id ?></a></li>
                <?php
                }
            }
            }
            echo '</ul>';
        }

        ?>

    </details>

    <details id="tempaltes" open>
        <summary>Templates</summary>
        <ul>
        <?php
        foreach ($templates as $template => $id) {
            if(!empty($id) && !is_bool($id)) {
                ?>
                <li><?= $template ?> : <?= $id ?> <a href="<?= $this->uart('artedit', $id) ?>">✎</a></li>
                <?php
            }
        }
        
        ?>
        </ul>
        
    </details>

        <h3>Authors</h3>


    <?php if($user->level() >= 4) { ?>


    <label for="authors">Invites editors</label>
    <select name="authors[]" id="authors">
    <option value="" selected>--add author--</option>
    <?php
    $notyetauthorlist = array_diff($editorlist, $art->authors());
        foreach ($notyetauthorlist as $author) {
            echo '<option value="'.$author.'" >'.$author.'</option>';
        }
    }
    ?>

    </select>
    <?php
        $alreadyauthorlist = array_intersect($editorlist, $art->authors());
        foreach ($alreadyauthorlist as $author) {
            ?>
            <div class="checkexternal">
            <?php if($user->level() >= 4) { ?>
            <input type="checkbox" name="authors[]" id="<?= $author ?>" value="<?= $author ?>" checked>
            <?php } ?>
            <label for="<?= $author ?>" >⬗ <?= $author ?></label>
            </div>
            <?php
        }
        ?>
    

    </div>

</div>