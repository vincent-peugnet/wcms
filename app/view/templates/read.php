<?php $this->layout('readerlayout') ?>

<?php
$this->start('head');

echo $head;

$this->stop();
?>



<?php $this->start('page') ?>

<body>


<?= $body ?>


</body>

<?php $this->stop() ?>