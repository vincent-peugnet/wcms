<?php if($opt->sortby() === $th) : ?>
    <i class="fa fa-sort-<?= $opt->order() > 0 ? 'asc' : 'desc' ?>"></i>
<?php endif ?>