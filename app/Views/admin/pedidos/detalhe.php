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
                                                <?= esc($produto['tamanho'] ?? '') ?><?= !empty($produto['tamanho']) && !empty($produto['cor']) ? ' / ' : '' ?><?= esc($produto['cor'] ?? '') ?>
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
                        <div class="mt-2" id="box-simular-webhook">
                            <button type="button" class="btn btn-sm btn-outline-success w-100 rounded-pill" id="btn-simular-aprovacao"
                                data-pedido-id="<?= esc($pedido['id']) ?>">
                                <i class="bi bi-play-circle me-1"></i>Simular Confirmação via Webhook
                            </button>
                            <div id="simular-feedback" class="mt-2 small d-none"></div>
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

                <!-- Código de Rastreio -->
                <?php if (!empty($pedido['codigo_rastreio'])): ?>
                <div class="mb-3">
                    <p class="fw-semibold mb-1" style="font-size:.875rem;">
                        <i class="bi bi-geo-alt-fill text-info me-1"></i>Código de Rastreamento
                    </p>
                    <div class="bg-info bg-opacity-10 border border-info-subtle rounded-3 px-3 py-2 d-flex align-items-center justify-content-between">
                        <span class="font-monospace fw-bold text-info" style="letter-spacing:2px;">
                            <?= esc($pedido['codigo_rastreio']) ?>
                        </span>
                        <a href="https://www.correios.com.br/rastreamento" target="_blank"
                           class="btn btn-xs btn-outline-info btn-sm py-0 px-2" style="font-size:11px;">
                            <i class="bi bi-box-arrow-up-right"></i> Rastrear
                        </a>
                    </div>
                </div>
                <hr>
                <?php endif; ?>

                <p class="fw-semibold mb-2" style="font-size:.875rem;">Atualizar Status</p>
                <?= form_open('admin/pedidos/atualizar-status/' . $pedido['id']) ?>
                    <div class="mb-2">
                        <select name="status" class="form-select form-select-sm" id="select-status-pedido">
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?= $status ?>"
                                    <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo Código de Rastreio (visível ao selecionar "enviado") -->
                    <div class="mb-2 d-none" id="campo-rastreio">
                        <input type="text"
                               name="codigo_rastreio"
                               class="form-control form-control-sm"
                               placeholder="Código de rastreio (ex: BR1234567SP)"
                               value="<?= esc($pedido['codigo_rastreio'] ?? '') ?>">
                        <div class="form-text">O código será enviado por e-mail ao cliente.</div>
                    </div>

                    <!-- Campo Motivo de Cancelamento (visível ao selecionar "cancelado") -->
                    <div class="mb-2 d-none" id="campo-cancelamento">
                        <textarea name="motivo_cancelamento"
                                  class="form-control form-control-sm"
                                  rows="2"
                                  placeholder="Motivo do cancelamento (opcional)"></textarea>
                        <div class="form-text">Será incluído no e-mail de cancelamento.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 w-100" id="btn-salvar-status">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                <?= form_close() ?>

                <hr>

                <!-- Reenvio de E-mails -->
                <p class="fw-semibold mb-2" style="font-size:.875rem;">
                    <i class="bi bi-envelope me-1 text-primary"></i>Reenviar Notificação
                </p>
                <div class="d-flex flex-column gap-2">
                    <form method="post" action="<?= site_url('admin/pedidos/reenviar-email/' . $pedido['id'] . '/criado') ?>" class="w-100">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100 text-start" id="btn-reenviar-criado">
                            <i class="bi bi-cart-check me-2 text-secondary"></i>🛒 Pedido Criado
                        </button>
                    </form>

                    <form method="post" action="<?= site_url('admin/pedidos/reenviar-email/' . $pedido['id'] . '/pago') ?>" class="w-100">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-success btn-sm w-100 text-start" id="btn-reenviar-pago">
                            <i class="bi bi-credit-card me-2 text-success"></i>✅ Pagamento Aprovado
                        </button>
                    </form>

                    <form method="post" action="<?= site_url('admin/pedidos/reenviar-email/' . $pedido['id'] . '/enviado') ?>" class="w-100">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-info btn-sm w-100 text-start" id="btn-reenviar-enviado">
                            <i class="bi bi-truck me-2 text-info"></i>🚚 Pedido Enviado
                        </button>
                    </form>

                    <form method="post" action="<?= site_url('admin/pedidos/reenviar-email/' . $pedido['id'] . '/cancelado') ?>" class="w-100">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-start" id="btn-reenviar-cancelado">
                            <i class="bi bi-x-circle me-2 text-danger"></i>❌ Cancelado
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Campos dinâmicos no formulário de status ─────────────────────────
    const selectStatus      = document.getElementById('select-status-pedido');
    const campoRastreio     = document.getElementById('campo-rastreio');
    const campoCancelamento = document.getElementById('campo-cancelamento');

    function toggleCamposStatus() {
        if (!selectStatus) return;
        const val = selectStatus.value;
        campoRastreio?.classList.toggle('d-none', val !== 'enviado');
        campoCancelamento?.classList.toggle('d-none', val !== 'cancelado');
    }

    selectStatus?.addEventListener('change', toggleCamposStatus);
    toggleCamposStatus(); // run on load in case status is already "enviado"

    // ── Simulação de Webhook ──────────────────────────────────────────────
    const btnSimular = document.getElementById('btn-simular-aprovacao');
    const feedback   = document.getElementById('simular-feedback');

    if (btnSimular) {
        btnSimular.addEventListener('click', async function (e) {
            e.preventDefault();
            const idPedido = parseInt(this.dataset.pedidoId, 10);

            btnSimular.disabled = true;
            btnSimular.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Simulando...';
            if (feedback) {
                feedback.className = 'mt-2 small d-none';
                feedback.innerHTML = '';
            }

            try {
                const response = await fetch('<?= site_url('api/webhook/simular') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pedido_id: idPedido,
                        status: 'approved',
                        evento: 'pago'
                    })
                });

                const data = await response.json();

                if (data.ok) {
                    if (feedback) {
                        feedback.className = 'alert alert-success py-2 px-3 small mt-2 mb-0 rounded-3';
                        feedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>' + (data.mensagem || 'Pagamento confirmado!');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    btnSimular.disabled = false;
                    btnSimular.innerHTML = '<i class="bi bi-play-circle me-1"></i>Simular Confirmação via Webhook';
                    if (feedback) {
                        feedback.className = 'alert alert-danger py-2 px-3 small mt-2 mb-0 rounded-3';
                        feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>' + (data.erro || 'Erro ao simular webhook.');
                    }
                }
            } catch (err) {
                console.error('Erro na simulação do webhook:', err);
                btnSimular.disabled = false;
                btnSimular.innerHTML = '<i class="bi bi-play-circle me-1"></i>Simular Confirmação via Webhook';
                if (feedback) {
                    feedback.className = 'alert alert-danger py-2 px-3 small mt-2 mb-0 rounded-3';
                    feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1"></i>Erro de conexão ao simular webhook.';
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>