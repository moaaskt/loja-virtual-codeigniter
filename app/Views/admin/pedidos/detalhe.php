<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1><i class="bi bi-receipt-cutoff text-primary me-2"></i><?= esc($title) ?></h1>
        <p class="text-muted small mb-0">Detalhes e gerenciamento do pedido</p>
    </div>
    <a href="<?= site_url('admin/pedidos') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btn-voltar-pedidos">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row g-4">

    <!-- ===== ORDER ITEMS ===== -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-cart3 text-primary me-2"></i>Itens do Pedido
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($produtos as $produto): ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= strpos($produto['imagem'], 'http') === 0
                                    ? esc($produto['imagem'])
                                    : base_url('uploads/produtos/' . esc($produto['imagem'])) ?>"
                                    alt="<?= esc($produto['nome']) ?>"
                                    class="img-thumb-table">
                                <div>
                                    <p class="fw-semibold mb-0"><?= esc($produto['nome']) ?></p>
                                    <?php if (!empty($produto['tamanho']) || !empty($produto['cor'])): ?>
                                        <small class="text-muted d-block mt-1">
                                            <span class="badge bg-light text-dark border">
                                                <?= !empty($produto['tamanho']) ? 'Tam: ' . esc($produto['tamanho']) : '' ?>
                                                <?= !empty($produto['tamanho']) && !empty($produto['cor']) ? ' | ' : '' ?>
                                                <?= !empty($produto['cor']) ? 'Cor: ' . esc($produto['cor']) : '' ?>
                                            </span>
                                        </small>
                                    <?php endif; ?>
                                    <small class="text-muted d-block mt-1">
                                        <?= esc($produto['quantidade']) ?> × R$ <?= esc(number_format($produto['preco_unitario'], 2, ',', '.')) ?>
                                    </small>
                                </div>
                            </div>
                            <strong class="text-nowrap">
                                R$ <?= esc(number_format($produto['preco_unitario'] * $produto['quantidade'], 2, ',', '.')) ?>
                            </strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- ===== ORDER DETAILS ===== -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-semibold">
                <i class="bi bi-person-fill text-primary me-2"></i>Cliente & Pedido
            </div>
            <div class="card-body">
                <dl class="row g-2 mb-3">
                    <dt class="col-5 text-muted small fw-normal">Cliente</dt>
                    <dd class="col-7 fw-semibold mb-0"><?= esc($pedido['cliente_nome']) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">E-mail</dt>
                    <dd class="col-7 mb-0" style="font-size:.875rem; word-break:break-all;"><?= esc($pedido['cliente_email']) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">Data</dt>
                    <dd class="col-7 mb-0" style="font-size:.875rem;">
                        <?= esc(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?>
                    </dd>

                    <?php if (!empty($pedido['cupom_codigo'])): ?>
                        <dt class="col-5 text-muted small fw-normal">Cupom</dt>
                        <dd class="col-7 mb-0 text-success fw-semibold" style="font-size:.875rem;">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                                <?= esc($pedido['cupom_codigo']) ?>
                            </span>
                            (- R$ <?= esc(number_format($pedido['desconto_valor'], 2, ',', '.')) ?>)
                        </dd>
                    <?php endif; ?>

                    <?php if (!empty($pedido['frete_modalidade']) || (float)($pedido['frete_valor'] ?? 0) > 0): ?>
                        <dt class="col-5 text-muted small fw-normal">Frete</dt>
                        <dd class="col-7 mb-0 text-muted" style="font-size:.875rem;">
                            <?= esc($pedido['frete_modalidade'] ?? 'Entrega') ?>: 
                            <strong class="text-dark">
                                <?= (float)$pedido['frete_valor'] === 0.0 ? 'Grátis' : 'R$ ' . number_format($pedido['frete_valor'], 2, ',', '.') ?>
                            </strong>
                        </dd>
                    <?php endif; ?>

                    <dt class="col-5 text-muted small fw-normal">Total</dt>
                    <dd class="col-7 mb-0 fs-5 fw-bold text-success">
                        R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?>
                    </dd>
                </dl>

                <hr>

                <!-- Informações de Pagamento -->
                <div class="mb-3">
                    <p class="fw-semibold mb-2" style="font-size:.875rem;">
                        <i class="bi bi-credit-card text-primary me-1"></i>Informações de Pagamento
                    </p>
                    <div class="bg-light p-3 rounded-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Método:</span>
                            <span class="badge bg-white text-dark border">
                                <?= esc(getMetodoPagamentoLabel($pedido['forma_pagamento'] ?? $pagamento['metodo'] ?? null)) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Status Pagamento:</span>
                            <span class="badge <?= getPagamentoStatusColorClass($pagamento['status'] ?? $pedido['status_pagamento'] ?? 'pendente') ?>">
                                <?= esc(ucfirst($pagamento['status'] ?? $pedido['status_pagamento'] ?? 'pendente')) ?>
                            </span>
                        </div>
                        <?php if (!empty($pagamento['transacao_id'])): ?>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">ID Transação:</span>
                                <span class="font-monospace text-truncate" style="max-width:140px;" title="<?= esc($pagamento['transacao_id']) ?>">
                                    <?= esc($pagamento['transacao_id']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($pagamento['cartao_ultimos_digitos'])): ?>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Cartão:</span>
                                <span>Final <?= esc($pagamento['cartao_ultimos_digitos']) ?> (<?= esc(strtoupper($pagamento['cartao_bandeira'] ?? '')) ?>) - <?= esc($pagamento['cartao_parcelas'] ?? 1) ?>x</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($pagamento['pago_em'])): ?>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Data Pagamento:</span>
                                <span><?= esc(date('d/m/Y H:i', strtotime($pagamento['pago_em']))) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (($pagamento['status'] ?? $pedido['status_pagamento'] ?? '') === 'pendente'): ?>
                        <div class="mt-2">
                            <?= form_open('api/webhook/simular') ?>
                                <input type="hidden" name="pedido_id" value="<?= esc($pedido['id']) ?>">
                                <input type="hidden" name="evento" value="pago">
                                <button type="submit" class="btn btn-sm btn-outline-success w-100 rounded-pill" id="btn-simular-aprovacao">
                                    <i class="bi bi-play-circle me-1"></i>Simular Confirmação via Webhook
                                </button>
                            <?= form_close() ?>
                        </div>
                    <?php endif; ?>
                </div>

                <hr>

                <!-- Endereço de Entrega -->
                <div class="mb-3">
                    <p class="fw-semibold mb-1" style="font-size:.875rem;">
                        <i class="bi bi-geo-alt text-primary me-1"></i>Endereço de Entrega
                    </p>
                    <p class="text-muted small mb-0 lh-sm">
                        <?= esc($pedido['logradouro'] ?? '') ?>, <?= esc($pedido['numero'] ?? '') ?><br>
                        <?= !empty($pedido['complemento']) ? esc($pedido['complemento']) . '<br>' : '' ?>
                        <?= esc($pedido['bairro'] ?? '') ?> — <?= esc($pedido['cidade'] ?? '') ?>/<?= esc($pedido['uf'] ?? '') ?><br>
                        CEP: <?= esc($pedido['cep'] ?? '') ?>
                    </p>
                </div>

                <hr>

                <p class="fw-semibold mb-2" style="font-size:.875rem;">Atualizar Status</p>
                <?= form_open('admin/pedidos/atualizar-status/' . $pedido['id']) ?>
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm" id="select-status-pedido">
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?= $status ?>"
                                    <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-shrink-0" id="btn-salvar-status">
                            Salvar
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>