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
    <ul>
    <?php
    foreach ($art->authors() as $author) {
        echo '<li>⬖ ' . $author .'</li>';
    }
    
    ?>
    </ul>

    <?php if($user->level() >= 4) { ?>

    <h3>Invites editors</h3>

    <label for="invites">Invites editors</label>
        <select name="invites[]" id="invites">
        <option value="" selected>--add invite user--</option>
        <?php
        $newinviteuserlist = array_diff($inviteuserlist, $art->invites());
        foreach ($newinviteuserlist as $inviteuser) {
            echo '<option value="'.$inviteuser.'" >'.$inviteuser.'</option>';
        }
        ?>
        </select>
        <?php
            $validateinviteusers = array_intersect($inviteuserlist, $art->invites());
            foreach ($validateinviteusers as $invite) {
                ?>
                <div class="checkexternal">
                <input type="checkbox" name="invites[]" id="<?= $invite ?>" value="<?= $invite ?>" checked>
                <label for="<?= $invite ?>" >⬗ <?= $invite ?></label>
                </div>
                <?php
            }
        ?>
    
        <?php } ?>

    </div>

</div>