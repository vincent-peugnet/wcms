<?php $this->layout('layout', ['title' => 'info', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'info']) ?>


<section class="info">

<h1>Info</h1>

<a href="https://github.com/vincent-peugnet/wcms" target="_blank">🐱‍👤 Github</a>

<a href="#">📕 Manual</a>
<a href="#">🌵 Website</a>

<h2>About</h2>

</section>
</body>

<?php $this->stop('page') ?>