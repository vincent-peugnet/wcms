<?php $this->layout('backlayout', ['title' => 'Documentation', 'stylesheets' => [$css . 'back.css', $css . 'info.css'], 'theme' => $theme]) ?>

<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'info', 'pagelist' => $pagelist]) ?>

<main class="main-info">

    <nav id="toc">

        <h2>Version <?= $version ?></h2>
        <div class="scroll">

            <p class="selector">
                <a
                    href="<?= $this->url('info') ?>"
                    <?= $view === 'manual' ? 'class="currentpage"' : '' ?>
                >
                    <!-- <i class="fa fa-book"></i> -->
                    user manual
                </a>

                <a
                    href="<?= $this->url('infoapi') ?>"
                    <?= $view === 'api' ? 'class="currentpage"' : '' ?>
                >
                    <!-- <i class="fa fa-book"></i> -->
                    API doc
                </a>
            </p>

            <h2>Summary</h2>

            <?= $summary ?>

            <h2>External links</h2>

            <ul class="summary">
                <li><a href="https://github.com/vincent-peugnet/wcms" target="_blank">🐱‍👤 Github</a></li>
                <li><a href="https://w.club1.fr" target="_blank">🌵 Website</a></li>
            </ul>
        </div>
    </nav>

    <section class="doc">

        <h1>Documentation</h1>
        
        <div class="scroll">
            <article id="manual">
                <?= $manual ?>
            </article>
        </div>

    </section>

</main>

<?php $this->stop('page') ?>
