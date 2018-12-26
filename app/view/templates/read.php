<?php $this->layout('readerlayout') ?>

<?php
$this->start('head');

if ($artexist) {
    if ($canread) {
        echo $head;
    } else {
        $this->insert('arthead', ['title' => $art->title(), 'description' => $art->description()]);
    }
} else {
    $this->insert('arthead', ['title' => $art->id(), 'description' => $alertnotexist]);
}



$this->stop();
?>


    






<?php $this->start('page') ?>

    <body>
        


        <?php 
        if ($readernav) {
            $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist, 'canedit' => $canedit]);
        }
        ?>
        


        <?php

        if ($artexist) {

            if ($canread) {
                echo $body;
            } else {
                echo '<h1>'.$alertprivate.'</h1>';
            }

        } else {
            if(!empty(Config::existnot())) {
                echo '<h1>' . Config::existnot() . '</h1>';
            }
            if ($user->iseditor()) {
                ?>
                <a href="<?= $this->uart('artadd', $art->id()) ?>">⭐ Create</a>            
                <?php
                }
        }



        ?>

        
    </body>

<?php $this->stop() ?>