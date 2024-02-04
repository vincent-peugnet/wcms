<?php $this->layout('layout', ['title' => 'Documentation', 'stylesheets' => [$css . 'back.css', $css . 'info.css']]) ?>


<?php $this->start('page') ?>


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
                <li><a href="https://w.club1.fr" target="_blank">🌵 Website</a></li>
                <li><a href="https://github.com/vincent-peugnet/wcms/blob/master/API.md" target="_blank">📕 API doc</a></li>
                </ul>

                <h2>Manual Summary</h2>

                <?= $summary ?>
            </div>
        </div>
    </div>
</nav>

<section class="info">

    <div class="block">
        <h1>Documentation</h1>
        <div class="scroll">


            <article id="manual">
                <?= $manual ?>
            </article>


        </div>
    </div>


</section>

</main>

<?php $this->stop('page') ?>
