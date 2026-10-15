<span class="poster">
    <i class="fa fa-<?= $media->type() === Wcms\Media::IMAGE ? 'film' : 'picture-o' ?>" title="movie poster syntax used"></i>
    <?= $posterlist[$media->filename()]['valid'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' ?>
</span>
