<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="border-bottom border-1 mb-4 pb-2">
                <h1 class="h2">
                    <a href="/debug"><?= $title ?></a> / <?= $class ?>
                </h1>
            </div>

            <?php if(count($methods) > 0): ?>

            <p>Select a method from below:</p>

            <div class="list-group">
            <?php foreach ($methods as $method): ?>
                <a href="<?= current_url() ?>/<?= $method ?>" class="list-group-item list-group-item-action"><?= str_replace('_', ' ', ucfirst($method)) ?></a>
            <?php endforeach; ?>
            </div>

            <?php else: ?>

                <p>No methods available.</p>

            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>