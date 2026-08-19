<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- Header do Pedido -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Início</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('minha-conta/pedidos') ?>" class="text-decoration-none">Meus Pedidos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pedido #<?= esc($pedido['id']) ?></li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-receipt-cutoff text-primary"></i>
                Pedido #<?= esc($pedido['id']) ?>
            </h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge <?= getStatusColorClass($pedido['status']) ?> fs-6 px-3 py-2 rounded-pill shadow-sm" id="badge-status-pedido">
                <?= esc(ucfirst($pedido['status'])) ?>
            </span>
        </div>
    </div>

    <div class="row g-4">

        <!-- Coluna Principal: Pagamento -->
        <div class="col-lg-7">

            <?php if (($pedido['forma_pagamento'] ?? '') === 'pix'): ?>
                <!-- ===== CARD PIX ===== -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-success text-white py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-qr-code fs-4"></i>
                            <h2 class="h5 mb-0 fw-bold">Pagamento via Pix</h2>
                        </div>
                        <span class="badge bg-white text-success fw-bold rounded-pill px-3 py-1">Instantâneo</span>
                    </div>

                    <div class="card-body p-4 text-center">

                        <?php if ($pedido['status'] === 'pago' || ($pagamento['status'] ?? '') === 'pago'): ?>
                            <!-- Status Já Pago -->
                            <div class="py-4">
                                <div class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                                </div>
                                <h3 class="fw-bold text-success">Pagamento Confirmado!</h3>
                                <p class="text-muted">Seu pagamento via Pix foi recebido e seu pedido já está sendo preparado.</p>
                                <div class="mt-4">
                                    <a href="<?= site_url('minha-conta/pedidos') ?>" class="btn btn-primary rounded-pill px-4">
                                        <i class="bi bi-box-seam me-2"></i>Ver Meus Pedidos
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Status Pendente: Exibe QR Code & Instruções -->
                            <p class="text-muted mb-3">
                                Abra o aplicativo do seu banco, escolha <strong>Pagar com Pix</strong> e escaneie o código abaixo:
                            </p>

                            <!-- QR Code Visual -->
                            <div class="d-inline-block p-3 bg-white border rounded-4 shadow-sm mb-3 position-relative" style="max-width:280px;">
                                <?php if (!empty($pagamento['pix_qrcode_base64'])): ?>
                                    <img src="<?= $pagamento['pix_qrcode_base64'] ?>" alt="QR Code Pix" class="img-fluid" style="width:240px; height:240px; object-fit:contain;">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width:240px; height:240px;">
                                        <i class="bi bi-qr-code text-muted" style="font-size:6rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Valor do Pix -->
                            <div class="mb-4">
                                <span class="text-muted small d-block">Valor a pagar:</span>
                                <strong class="fs-3 text-success">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
                            </div>

                            <!-- Código Pix Copia e Cola -->
                            <div class="text-start mb-4">
                                <label for="input-pix-copiacola" class="form-label small fw-semibold text-muted">
                                    <i class="bi bi-clipboard-check me-1"></i>Código Pix Copia e Cola:
                                </label>
                                <div class="input-group">
                                    <input type="text" id="input-pix-copiacola" class="form-control font-monospace text-muted small"
                                        value="<?= esc($pagamento['pix_copiacola'] ?? '') ?>" readonly>
                                    <button class="btn btn-primary px-3 fw-semibold" type="button" id="btn-copiar-pix">
                                        <i class="bi bi-copy me-1"></i> Copiar Código
                                    </button>
                                </div>
                                <div id="copiar-feedback" class="small text-success mt-1 d-none fw-semibold">
                                    <i class="bi bi-check-lg me-1"></i>Código Pix copiado para a área de transferência!
                                </div>
                            </div>

                            <!-- Temporizador de Expiração -->
                            <div class="alert alert-warning border-0 rounded-3 py-2 px-3 d-flex align-items-center justify-content-center gap-2 mb-3">
                                <i class="bi bi-clock-history fs-5"></i>
                                <span class="small">
                                    O código expira em <strong id="pix-timer">30:00</strong>.
                                </span>
                            </div>

                            <!-- Status de Verificação Automática -->
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted small mt-3">
                                <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                                <span>Aguardando confirmação do pagamento pelo banco...</span>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>

            <?php else: ?>
                <!-- ===== CARD CARTÃO DE CRÉDITO ===== -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card-2-front fs-4"></i>
                            <h2 class="h5 mb-0 fw-bold">Pagamento com Cartão de Crédito</h2>
                        </div>
                        <span class="badge bg-white text-primary fw-bold rounded-pill px-3 py-1">
                            <?= esc(strtoupper($pagamento['cartao_bandeira'] ?? 'Cartão')) ?>
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                            <i class="bi bi-credit-card fs-1 text-primary"></i>
                            <div>
                                <div class="fw-bold">
                                    Final <?= esc($pagamento['cartao_ultimos_digitos'] ?? '****') ?>
                                    (<?= esc(strtoupper($pagamento['cartao_bandeira'] ?? 'Crédito')) ?>)
                                </div>
                                <small class="text-muted">
                                    <?= esc($pagamento['cartao_parcelas'] ?? 1) ?>x de R$ <?= number_format($pedido['valor_total'] / ($pagamento['cartao_parcelas'] ?? 1), 2, ',', '.') ?>
                                </small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge <?= getPagamentoStatusColorClass($pagamento['status'] ?? 'pendente') ?> px-3 py-2 rounded-pill">
                                    <?= esc(ucfirst($pagamento['status'] ?? 'pendente')) ?>
                                </span>
                            </div>
                        </div>

                        <dl class="row g-2 mb-0 small">
                            <dt class="col-sm-4 text-muted">ID da Transação:</dt>
                            <dd class="col-sm-8 font-monospace fw-semibold"><?= esc($pagamento['transacao_id'] ?? '-') ?></dd>

                            <dt class="col-sm-4 text-muted">Data do Pagamento:</dt>
                            <dd class="col-sm-8"><?= !empty($pagamento['pago_em']) ? esc(date('d/m/Y H:i', strtotime($pagamento['pago_em']))) : 'Processando' ?></dd>

                            <dt class="col-sm-4 text-muted">Total Pago:</dt>
                            <dd class="col-sm-8 fw-bold text-success fs-6">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></dd>
                        </dl>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informações de Entrega -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h3 class="h6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-primary"></i>Endereço de Entrega
                </h3>
                <p class="text-muted mb-0 small lh-base">
                    <strong><?= esc($pedido['logradouro']) ?>, Nº <?= esc($pedido['numero']) ?></strong>
                    <?= !empty($pedido['complemento']) ? ' - ' . esc($pedido['complemento']) : '' ?><br>
                    <?= esc($pedido['bairro']) ?> — <?= esc($pedido['cidade']) ?>/<?= esc($pedido['uf']) ?><br>
                    CEP: <?= esc($pedido['cep']) ?>
                </p>
            </div>

        </div>

        <!-- Coluna Lateral: Resumo do Pedido -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top:90px;">
                <h3 class="h6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-bag-check text-primary"></i>Itens do Pedido (<?= count($itens ?? []) ?>)
                </h3>

                <ul class="list-group list-group-flush mb-3">
                    <?php if (!empty($itens)): ?>
                        <?php foreach ($itens as $it): ?>
                            <li class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= strpos($it['imagem'], 'http') === 0 ? esc($it['imagem']) : base_url('uploads/produtos/' . esc($it['imagem'])) ?>"
                                        alt="<?= esc($it['nome']) ?>"
                                        style="width: 45px; height: 45px; object-fit: cover;" class="rounded-3 border">
                                    <div>
                                        <div class="fw-semibold small text-truncate" style="max-width:180px;"><?= esc($it['nome']) ?></div>
                                        <small class="text-muted" style="font-size:0.75rem;">
                                            <?= !empty($it['tamanho']) ? 'Tam: ' . esc($it['tamanho']) . ' ' : '' ?>
                                            <?= !empty($it['cor']) ? 'Cor: ' . esc($it['cor']) . ' ' : '' ?>
                                            Qtd: <?= esc($it['quantidade']) ?>
                                        </small>
                                    </div>
                                </div>
                                <span class="fw-semibold small text-nowrap">
                                    R$ <?= number_format($it['preco_unitario'] * $it['quantidade'], 2, ',', '.') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <!-- Linhas Financeiras -->
                <div class="border-top pt-3 small">
                    <?php if (!empty($pedido['cupom_codigo']) && (float)($pedido['desconto_valor'] ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span><i class="bi bi-tag-fill me-1"></i>Desconto (<?= esc($pedido['cupom_codigo']) ?>)</span>
                            <span>- R$ <?= number_format($pedido['desconto_valor'], 2, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pedido['frete_modalidade']) || (float)($pedido['frete_valor'] ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span><i class="bi bi-truck me-1"></i>Frete (<?= esc($pedido['frete_modalidade'] ?? 'Entrega') ?>)</span>
                            <span class="text-success fw-semibold">
                                <?= (float)$pedido['frete_valor'] === 0.0 ? 'GRÁTIS' : 'R$ ' . number_format($pedido['frete_valor'], 2, ',', '.') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between fw-bold fs-5 text-dark border-top pt-2 mt-2">
                        <span>Total</span>
                        <span class="text-success">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <a href="<?= site_url('minha-conta/pedidos') ?>" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Voltar para Meus Pedidos
                    </a>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Modal de Sucesso de Pagamento Aprovado Instantaneamente -->
<div class="modal fade" id="modalPagamentoAprovado" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                </div>
                <h3 class="fw-bold text-success mb-2">Pagamento Confirmado!</h3>
                <p class="text-muted mb-4">
                    Recebemos o seu pagamento via Pix com sucesso. Seu pedido está em processamento!
                </p>
                <a href="<?= site_url('pedido/sucesso/' . $pedido['id']) ?>" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                    Ver Comprovante do Pedido
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------------------
    // Copiar Código Pix Copia e Cola
    // ----------------------------------------------------------------
    const btnCopiar = document.getElementById('btn-copiar-pix');
    const inputPix  = document.getElementById('input-pix-copiacola');
    const feedback  = document.getElementById('copiar-feedback');

    if (btnCopiar && inputPix) {
        btnCopiar.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(inputPix.value);
                if (feedback) feedback.classList.remove('d-none');
                btnCopiar.innerHTML = '<i class="bi bi-check2 me-1"></i> Copiado!';
                btnCopiar.classList.replace('btn-primary', 'btn-success');

                setTimeout(() => {
                    btnCopiar.innerHTML = '<i class="bi bi-copy me-1"></i> Copiar Código';
                    btnCopiar.classList.replace('btn-success', 'btn-primary');
                }, 3000);
            } catch (err) {
                // Fallback para seleção manual
                inputPix.select();
                document.execCommand('copy');
                if (feedback) feedback.classList.remove('d-none');
            }
        });
    }

    // ----------------------------------------------------------------
    // Temporizador de 30 minutos
    // ----------------------------------------------------------------
    const timerElem = document.getElementById('pix-timer');
    if (timerElem) {
        let totalSeconds = 30 * 60;
        const timerInterval = setInterval(() => {
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                timerElem.textContent = 'Expirado';
                return;
            }
            totalSeconds--;
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            timerElem.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }, 1000);
    }

    // ----------------------------------------------------------------
    // Polling Automático de Status de Pagamento (a cada 3.5s)
    // ----------------------------------------------------------------
    const pedidoId = <?= (int) $pedido['id'] ?>;
    const statusAtual = '<?= esc($pedido['status']) ?>';

    if (statusAtual === 'pendente') {
        const pollingInterval = setInterval(async () => {
            try {
                const res = await fetch(`<?= site_url('api/pedidos') ?>/${pedidoId}/status-pagamento`);
                const data = await res.json();

                if (data.ok && data.pago) {
                    clearInterval(pollingInterval);

                    const badge = document.getElementById('badge-status-pedido');
                    if (badge) {
                        badge.className = 'badge bg-success fs-6 px-3 py-2 rounded-pill shadow-sm';
                        badge.textContent = 'Pago';
                    }

                    // Exibe o modal de aprovação e redireciona após 2s
                    const modalEl = document.getElementById('modalPagamentoAprovado');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        setTimeout(() => {
                            window.location.href = `<?= site_url('pedido/sucesso') ?>/${pedidoId}`;
                        }, 2500);
                    } else {
                        window.location.href = `<?= site_url('pedido/sucesso') ?>/${pedidoId}`;
                    }
                }
            } catch (err) {
                console.warn('Erro ao checar status de pagamento:', err);
            }
        }, 3500);
    }
});
</script>
<?= $this->endSection() ?>
