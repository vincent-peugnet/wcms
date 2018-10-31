<?php $this->layout('layout', ['title' => $art->title(), 'description' => $art->description()]) ?>




    






<?php $this->start('page') ?>

    <body>
        


        <?php $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist]) ?>
        


        <?php

        if($artexist) {

            if($display) { 
                $this->insert('readart', ['art' => $art]);
            } else {
                echo '<h1>You dont have enought rights to see this article</h1>';
            }

        } else {
            echo '<h1>This article does not exist yet</h1>';
            if ($cancreate) {
                $this->insert('readcreate', ['id' => $art->id()]);
            }
        }



        ?>

        
    </body>

<?php $this->stop() ?>