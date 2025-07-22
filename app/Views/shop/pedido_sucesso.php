<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
    <?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
   <div class="container text-center mt-5">
       <h1 class="text-success"><?= esc($title) ?></h1>
       <p>Obrigado por comprar conosco! Seu pedido foi registrado e está sendo processado.</p>
       <a href="<?= site_url('/') ?>" class="btn btn-primary mt-3">Voltar para a Vitrine</a>
   </div>
<?= $this->endSection() ?>