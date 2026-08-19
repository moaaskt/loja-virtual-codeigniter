<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4" style="max-width: 680px;">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width:80px; height:80px;">
                <i class="bi bi-bag-check-fill fs-1"></i>
            </div>
        </div>

        <h1 class="fw-bold mb-2 text-dark" style="font-size:1.6rem; letter-spacing:-.025em;">
            <?= esc($title) ?>
        </h1>

        <?php if (!empty($pedido)): ?>
            <p class="text-muted mb-4">
                Pedido <strong>#<?= esc($pedido['id']) ?></strong> confirmado em <?= esc(date('d/m/Y \à\s H:i', strtotime($pedido['criado_em']))) ?>.
            </p>

            <!-- Card com Resumo do Pagamento -->
            <div class="card bg-light border-0 rounded-3 p-3 mb-4 text-start">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Forma de Pagamento:</span>
                    <span class="badge bg-white text-dark border fw-semibold">
                        <i class="bi <?= ($pedido['forma_pagamento'] === 'pix') ? 'bi-qr-code text-success' : 'bi-credit-card text-primary' ?> me-1"></i>
                        <?= esc(getMetodoPagamentoLabel($pedido['forma_pagamento'])) ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Status do Pagamento:</span>
                    <span class="badge <?= getPagamentoStatusColorClass($pedido['status_pagamento'] ?? $pedido['status']) ?>">
                        <?= esc(ucfirst($pedido['status_pagamento'] ?? $pedido['status'])) ?>
                    </span>
                </div>
                <?php if (!empty($pagamento['cartao_ultimos_digitos'])): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Cartão:</span>
                        <span class="font-monospace small">Final <?= esc($pagamento['cartao_ultimos_digitos']) ?> (<?= esc(strtoupper($pagamento['cartao_bandeira'] ?? '')) ?>) - <?= esc($pagamento['cartao_parcelas'] ?? 1) ?>x</span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-1">
                    <span class="fw-bold text-dark">Total do Pedido:</span>
                    <strong class="text-success fs-5">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted mb-4 lh-lg">
                Obrigado por comprar conosco!<br>
                Seu pedido foi registrado e está sendo processado.
            </p>
        <?php endif; ?>

        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <?php if (!empty($pedido) && ($pedido['forma_pagamento'] ?? '') === 'pix' && $pedido['status'] === 'pendente'): ?>
                <a href="<?= site_url('pedido/pagamento/' . $pedido['id']) ?>" class="btn btn-success rounded-pill px-4" id="btn-ver-pix">
                    <i class="bi bi-qr-code me-2"></i>Ver QR Code Pix
                </a>
            <?php endif; ?>
            <a href="<?= site_url('minha-conta/pedidos') ?>" class="btn btn-outline-primary rounded-pill px-4" id="btn-meus-pedidos">
                <i class="bi bi-receipt me-2"></i>Meus Pedidos
            </a>
            <a href="<?= site_url('/') ?>" class="btn btn-primary rounded-pill px-4" id="btn-voltar-loja">
                <i class="bi bi-shop me-2"></i>Continuar comprando
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>