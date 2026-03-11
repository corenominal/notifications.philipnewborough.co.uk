<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="border-bottom border-1 mb-4 pb-2">
                <h1 class="h2"><?= $title ?></h1>
            </div>

            <?php if(count($files) > 0): ?>

            <p>Select a controller from below or use filter:</p>

            <div class="mb-3">
                <label class="visually-hidden" for="filter">Filter</label>
                <input type="text" class="form-control" id="filter" placeholder="Filter controllers">
            </div>

            <div class="list-group">
            <?php foreach ($files as $file): ?>
                <a href="<?= current_url() ?>/<?= $file ?>" class="list-group-item list-group-item-action"><?= str_replace('_', ' ', ucfirst($file)) ?></a>
            <?php endforeach; ?>
            </div>

            <div id="matches"></div>

            <?php else: ?>

                <p>No controllers available.</p>

            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>