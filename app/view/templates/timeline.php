<?php $this->layout('layout', ['title' => 'timeline', 'css' => $css . 'home.css', 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'timeline']) ?>

<?php if($user->iseditor()) { ?>

<main class="timeline">

<h1>Timeline</h1>

<ul>

<?php
foreach ($eventlist as $event) {
    if($user->id() === $event->user()) {
        $class = 'class="self event"';
    } else {
        $class = 'class="event"';
    }
    echo '<li '. $class .'>';
    switch ($event->type()) {
        case 'message':
            echo '<h3>'. $event->user() .'</h3>';
            echo '<p>'. $event->message() .'</p>';
            echo '<i>'. $event->date('hrdi') .' ago</i>';
            break;
        
        default:
            
            break;
    echo '</li>';
    }
}
?>

</ul>

<form action="<?= $this->url('timelineadd') ?>" method="post">

<input type="hidden" name="type" value="message">

<input type="hidden" name="user" value="<?= $user->id() ?>">

<label for="message">message</label>
<textarea name="message" id="message" cols="30" rows="10"></textarea>
<input type="submit" value="send">
</form>

</main>

<?php } ?>

</body>



<?php $this->stop() ?>