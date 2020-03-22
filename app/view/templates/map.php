<style>
    #graph {
        height: 100%;
        width: 100%;
    }
</style>

<div id="graph"></div>

<script>
    var data = <?= $json ?>;
    console.log(data);
</script>

<script src="<?= Wcms\Model::jspath() ?>map.bundle.js"></script>