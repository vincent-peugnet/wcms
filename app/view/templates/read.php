<?php $this->layout('readerlayout') ?>

<?php
$this->start('head');

echo $head;

$this->stop();
?>



<?php $this->start('page') ?>

<body>



    <?php
    if ($readernav) {
        $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist, 'canedit' => $canedit]);
    }



    echo $body;

    ?>


</body>

<?php $this->stop() ?>