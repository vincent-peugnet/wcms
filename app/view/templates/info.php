<?php $this->layout('layout', ['title' => 'info', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'info', 'pagelist' => $pagelist]) ?>


<main class="info">

<nav class="info">
    <div class="block">
        <div class="scroll">

            <h2>Version</h2>

            <?= $version ?>



            <h2>Links</h2>

                <ul>
                <li><a href="https://github.com/vincent-peugnet/wcms" target="_blank">🐱‍👤 Github</a></li>
                <li><a href="https://w-cms.top" target="_blank">🌵 Website</a></li>
                </ul>

                <h2>Manual Summary</h2>

                <?= $summary ?>
            </div>
        </div>
    </div>
</nav>

<section class="info">

    <div class="block">
        <h1>Info</h1>
        <div class="scroll">
            <article>




                <h2>About</h2>

                <h3>W-cms was made using these open sources and free components :</h3>

                <ul>
                <li><a href="https://github.com/jamesmoss/flywheel" target="_blank">🎡 James Moss's Flywheel Database</a> <i>as json noSQL flatfile database engine</i></li>
                <li><a href="https://github.com/michelf/php-markdown" target="_blank">📝 Michel Fortin's Markdown Extra</a> <i>markdown library</i></li>
                <li><a href="https://github.com/thephpleague/plates" target="_blank">🎨 Plates</a> <i>as templating engine</i></li>
                <li><a href="https://github.com/dannyvankooten/AltoRouter">🐶 Alto Router</a> <i>as router engine</i></li>
                </ul>

                <h3>Special thanks to :</h3>

                <a href="https://nicolas.club1.fr" target="_blank">🚲 Nicolas Peugnet</a>

            </article>

            <article id="manual">
                <?= $manual ?>
            </article>


        </div>
    </div>


</section>

</main>
</body>

<?php $this->stop('page') ?>