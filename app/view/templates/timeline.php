<?php $this->layout('layout', ['title' => 'timeline', 'css' => $css . 'home.css', 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'timeline']) ?>

<?php if($user->iseditor()) { ?>

<main class="timeline">

<section>

<article id="live">

<h1>Timeline</h1>

<ul>

<?php
foreach ($groupedeventlist as $eventuser) {

        if($user->id() === $eventuser['user']) {
            $class = 'class="self user"';
        } else {
            $class = 'class="user"';
        }
        echo '<li '. $class .'>';
        echo '<h3>'. $eventuser['user'] .'</h3>';
        echo' <ul>';
    foreach ($eventuser as $key => $event) {
        if($key !== 'user') {
            echo '<li class="event">';
            switch ($event->type()) {
                case 'message':
                echo '<p class="eline">'. $event->message() .'</p>';
                break;
            }

            ?>


            <?= !empty($event->clap()) ? '<b class="eline">'. $event->clap() .'</b>' : '' ?>

            <span class="details">

            <?php if($user->id() !== $eventuser['user']) {?>
            <form class="eline" method="post" action="<?= $this->url('timelineclap') ?>">
            <input type="hidden" name="id" value="<?= $event->id() ?>">
            <input type="submit" name="clap" value="👌">
            </form>
            <?php } ?>


            <i class="eline"><?= $event->date('hrdi') ?> ago</i>
            
            </span>

            </li>

            <?php
        }
    }
    echo '</ul></li>';
}
?>

</ul>

</article>

<article id="message">

<h2>Message</h2>

<form action="<?= $this->url('timelineadd') ?>" method="post">

<input type="hidden" name="type" value="message">

<input type="hidden" name="user" value="<?= $user->id() ?>">

<label for="message">message</label>
<textarea name="message" id="message" cols="30" rows="10" autofocus></textarea>
<input type="submit" value="send">
</form>

</main>

<?php } ?>

</article>

</section>

</body>



<?php $this->stop() ?>