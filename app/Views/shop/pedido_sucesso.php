<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="success-card">
    <div class="success-icon">
        <i class="bi bi-bag-check-fill"></i>
    </div>
    <h1 class="fw-bold mb-2" style="font-size:1.5rem; letter-spacing:-.025em; color:#065f46;">
        <?= esc($title) ?>
    </h1>
    <p class="text-muted mb-4 lh-lg">
        Obrigado por comprar conosco!<br>
        Seu pedido foi registrado e está sendo processado.
    </p>
    <div class="d-flex gap-2 justify-content-center flex-wrap">
        <a href="<?= site_url('minha-conta/pedidos') ?>" class="btn btn-outline-primary rounded-pill px-4" id="btn-meus-pedidos">
            <i class="bi bi-receipt me-2"></i>Meus Pedidos
        </a>
        <a href="<?= site_url('/') ?>" class="btn btn-primary rounded-pill px-4" id="btn-voltar-loja">
            <i class="bi bi-shop me-2"></i>Continuar comprando
        </a>
    </div>
</div>

<?= $this->endSection() ?>